<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProviderSelectorContract;
use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Enums\ServiceStatus;
use App\Exceptions\DomainRuleViolation;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;
use App\Services\Providers\ServiceProviderFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ServiceLifecycle
{
    public function __construct(private readonly ProviderSelectorContract $selector, private readonly ServiceProviderFactory $factory) {}

    public function create(TelegramAccount $account, Plan $plan, ?int $orderId = null, array $context = []): Service
    {
        $selection = $this->selector->select($account, $plan, ServiceProviderOperation::CREATE, $context['region'] ?? null);
        $provider = $selection->provider; $providerAccount = $selection->account;
        $key = $context['idempotency_key'] ?? ('service:create:'.Str::uuid());
        $result = $this->factory->make($provider)->create($account, $plan, new ServiceProviderContext($provider, $providerAccount, $key, $context['inbounds'] ?? [], $context));

        return DB::transaction(function () use ($account, $plan, $orderId, $context, $provider, $providerAccount, $result, $key): Service {
            return Service::query()->create([
                'uuid' => (string) Str::uuid(), 'telegram_account_id' => $account->getKey(), 'order_id' => $orderId,
                'service_provider_id' => $provider->getKey(), 'provider_account_id' => $providerAccount?->getKey(),
                'status' => $result->data['status'] ?? ServiceStatus::Active->value, 'external_id' => $result->externalId,
                'external_reference' => $result->externalReference, 'capacity' => $result->data['capacity'] ?? $plan->capacity,
                'starts_at' => now(), 'expires_at' => now()->addDays((int) $plan->duration), 'provisioning_key' => $key,
                'metadata' => [...$context, 'provider_result' => $result->data],
            ]);
        });
    }

    public function renew(Service $service, Plan $plan): Service
    {
        $result = $this->factory->make($service->provider)->renew($service, $plan, $this->contextFor($service, ServiceProviderOperation::RENEW));
        $service->forceFill(['status' => $result->data['status'] ?? $service->status, 'expires_at' => $result->data['expires_at'] ?? $service->expires_at])->save();
        return $service->fresh();
    }

    public function extend(Service $service, int $days): Service
    {
        if ($days <= 0) throw new DomainRuleViolation('Extension days must be positive.');
        $result = $this->factory->make($service->provider)->extend($service, $days, $this->contextFor($service, ServiceProviderOperation::EXTEND));
        $service->forceFill(['expires_at' => $result->data['expires_at'] ?? $service->expires_at])->save();
        return $service->fresh();
    }

    public function addCapacity(Service $service, int $capacity): Service
    {
        if ($capacity <= 0) throw new DomainRuleViolation('Additional capacity must be positive.');
        $result = $this->factory->make($service->provider)->addCapacity($service, $capacity, $this->contextFor($service, ServiceProviderOperation::ADD_CAPACITY));
        $service->forceFill(['capacity' => $result->data['capacity'] ?? ((int) $service->capacity + $capacity)])->save();
        return $service->fresh();
    }

    public function disable(Service $service): Service
    {
        $result = $this->factory->make($service->provider)->disable($service, $this->contextFor($service, ServiceProviderOperation::DISABLE));
        $service->forceFill(['status' => $result->data['status'] ?? ServiceStatus::Disabled->value])->save();
        return $service->fresh();
    }

    public function delete(Service $service): void
    {
        $this->factory->make($service->provider)->delete($service, $this->contextFor($service, ServiceProviderOperation::DELETE));
        $service->delete();
    }

    public function status(Service $service): array
    {
        return $this->factory->make($service->provider)->status($service, $this->contextFor($service, ServiceProviderOperation::STATUS))->data;
    }

    private function contextFor(Service $service, ServiceProviderOperation $operation): ServiceProviderContext
    {
        return new ServiceProviderContext($service->provider, $service->providerAccount, 'service:'.$service->getKey().':'.$operation->value);
    }
}
