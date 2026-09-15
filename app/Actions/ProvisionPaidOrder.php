<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\ProviderSelectorContract;
use App\DTOs\ServiceProviderContext;
use App\Enums\OrderStatus;
use App\Enums\ServiceProviderOperation;
use App\Enums\ServiceStatus;
use App\Exceptions\ServiceProviderException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\ServiceOperation;
use App\Services\Providers\ServiceProviderFactory;
use Illuminate\Support\Str;

final class ProvisionPaidOrder
{
    public function __construct(
        private readonly ProviderSelectorContract $selector,
        private readonly ServiceProviderFactory $factory,
    ) {}

    /** @return array<int, Service> */
    public function execute(Order $order): array
    {
        if ($order->status !== OrderStatus::Paid->value) {
            throw new ServiceProviderException('Only paid orders can be provisioned.', ServiceProviderOperation::CREATE);
        }

        $order->loadMissing(['telegramAccount', 'items.plan']);
        $services = [];
        foreach ($order->items as $item) {
            if ($item instanceof OrderItem && $item->plan !== null) {
                $services[] = $this->provisionItem($order, $item);
            }
        }
        return $services;
    }

    private function provisionItem(Order $order, OrderItem $item): Service
    {
        $key = 'order:'.$order->getKey().':item:'.$item->getKey().':create';
        $existing = Service::query()->where('provisioning_key', $key)->with(['provider', 'providerAccount'])->first();
        if ($existing && $existing->status === ServiceStatus::Active->value && $existing->external_id !== null) {
            return $existing;
        }

        if ($order->telegramAccount === null) {
            throw new ServiceProviderException('Paid order has no Telegram account.', ServiceProviderOperation::CREATE);
        }

        if ($existing?->provider !== null) {
            $provider = $existing->provider;
            $account = $existing->providerAccount;
        } else {
            $selection = $this->selector->select($order->telegramAccount, $item->plan, ServiceProviderOperation::CREATE);
            $provider = $selection->provider;
            $account = $selection->account;
        }

        $service = $existing ?? Service::query()->create([
            'uuid' => (string) Str::uuid(),
            'telegram_account_id' => $order->telegramAccount->getKey(),
            'order_id' => $order->getKey(),
            'order_item_id' => $item->getKey(),
            'plan_id' => $item->plan->getKey(),
            'service_provider_id' => $provider->getKey(),
            'provider_account_id' => $account?->getKey(),
            'status' => ServiceStatus::Pending->value,
            'capacity' => (int) ($item->plan->capacity ?? 0),
            'used_capacity' => 0,
            'is_trial' => (bool) ($item->plan->is_trial ?? false),
            'starts_at' => now(),
            'expires_at' => now()->addDays((int) ($item->plan->duration ?? 0)),
            'provisioning_key' => $key,
            'metadata' => ['provider_driver' => $provider->driver],
        ]);

        $operation = ServiceOperation::query()->firstOrCreate(
            ['idempotency_key' => $key],
            [
                'service_id' => $service->getKey(),
                'operation' => ServiceProviderOperation::CREATE->value,
                'status' => 'pending',
                'service_provider_id' => $provider->getKey(),
                'provider_account_id' => $account?->getKey(),
                'started_at' => now(),
                'request_metadata' => ['plan_id' => $item->plan->getKey()],
            ],
        );

        if ($operation->status === 'completed' && $service->external_id !== null) {
            return $service->fresh();
        }

        try {
            $result = $this->factory->make($provider)->create(
                $order->telegramAccount,
                $item->plan,
                new ServiceProviderContext($provider, $account, $key, [], [
                    'service_id' => $service->getKey(),
                    'order_id' => $order->getKey(),
                ]),
            );

            $service->update([
                'status' => $result->data['status'] ?? ServiceStatus::Active->value,
                'external_id' => $result->externalId,
                'external_reference' => $result->externalReference,
                'capacity' => $result->data['capacity'] ?? $service->capacity,
                'metadata' => [...($service->metadata ?? []), 'provider_result' => $result->data],
            ]);
            $operation->update([
                'status' => 'completed',
                'completed_at' => now(),
                'response_metadata' => $result->data,
                'error_code' => null,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            $service->update(['status' => ServiceStatus::Failed->value]);
            $operation->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_code' => $e::class,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $service->fresh();
    }
}
