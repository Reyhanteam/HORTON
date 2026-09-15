<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ServiceProviderContract;
use App\Enums\ServiceStatus;
use App\Exceptions\DomainRuleViolation;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;

final class ServiceLifecycle
{
    public function __construct(private readonly ServiceProviderContract $provider) {}

    public function create(TelegramAccount $account, Plan $plan, ?int $orderId = null, array $context = []): Service
    {
        return DB::transaction(function () use ($account, $plan, $orderId, $context): Service {
            $result = $this->provider->create($account, $plan, $context);
            $service = Service::query()->create([
                'telegram_account_id' => $account->getKey(),
                'order_id' => $orderId,
                'service_provider_id' => $context['service_provider_id'] ?? null,
                'status' => $result['status'] ?? ServiceStatus::Active->value,
                'capacity' => $result['capacity'] ?? $plan->capacity,
                'starts_at' => now(),
                'expires_at' => now()->addDays((int) $plan->duration),
                'metadata' => array_merge($context, ['provider' => $result]),
            ]);

            return $service->fresh();
        });
    }

    public function renew(Service $service, Plan $plan): Service
    {
        $result = $this->provider->renew($service, $plan);
        $service->forceFill(['status' => $result['status'] ?? $service->status, 'expires_at' => $result['expires_at'] ?? $service->expires_at])->save();
        return $service->fresh();
    }

    public function extend(Service $service, int $days): Service
    {
        if ($days <= 0) throw new DomainRuleViolation('Extension days must be positive.');
        $result = $this->provider->extend($service, $days);
        $service->forceFill(['expires_at' => $result['expires_at'] ?? $service->expires_at])->save();
        return $service->fresh();
    }

    public function addCapacity(Service $service, int $capacity): Service
    {
        if ($capacity <= 0) throw new DomainRuleViolation('Additional capacity must be positive.');
        $result = $this->provider->addCapacity($service, $capacity);
        $service->forceFill(['capacity' => $result['capacity'] ?? ((int) $service->capacity + $capacity)])->save();
        return $service->fresh();
    }

    public function disable(Service $service): Service
    {
        $result = $this->provider->disable($service);
        $service->forceFill(['status' => $result['status'] ?? ServiceStatus::Disabled->value])->save();
        return $service->fresh();
    }

    public function delete(Service $service): void
    {
        $this->provider->delete($service);
        $service->delete();
    }

    public function status(Service $service): array
    {
        return $this->provider->status($service);
    }
}
