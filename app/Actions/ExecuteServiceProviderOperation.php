<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Enums\ServiceStatus;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\ServiceOperation;
use App\Services\Providers\ServiceProviderFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ExecuteServiceProviderOperation
{
    public function __construct(private readonly ServiceProviderFactory $factory) {}

    public function execute(
        Service $service,
        ServiceProviderOperation $operation,
        ?Plan $plan = null,
        int $value = 0,
        ?string $idempotencyKey = null,
    ): Service {
        $service->loadMissing(['provider', 'providerAccount', 'telegramAccount', 'plan']);

        if ($service->provider === null) {
            throw new ServiceProviderException('Service has no provider instance.', $operation);
        }

        if ($service->telegramAccount === null) {
            throw new ServiceProviderException('Service has no Telegram account.', $operation);
        }

        $key = $idempotencyKey ?? $this->defaultKey($service, $operation, $value, $plan);
        $operationRecord = $this->claimOperation($service, $operation, $key, $plan, $value);

        if ($operationRecord->status === 'completed') {
            return $service->fresh();
        }

        try {
            $provider = $this->factory->make($service->provider);
            $context = new ServiceProviderContext(
                $service->provider,
                $service->providerAccount,
                $key,
                (array) (($service->metadata ?? [])['inbound_ids'] ?? []),
                ['service_id' => $service->getKey(), 'operation_id' => $operationRecord->getKey()],
            );

            $result = match ($operation) {
                ServiceProviderOperation::GET => $provider->get($service, $context),
                ServiceProviderOperation::STATUS => $provider->status($service, $context),
                ServiceProviderOperation::RENEW => $provider->renew($service, $plan ?? $service->plan, $context),
                ServiceProviderOperation::EXTEND => $provider->extend($service, $value, $context),
                ServiceProviderOperation::ADD_CAPACITY => $provider->addCapacity($service, $value, $context),
                ServiceProviderOperation::DISABLE => $provider->disable($service, $context),
                ServiceProviderOperation::DELETE => $provider->delete($service, $context),
                ServiceProviderOperation::CREATE => throw new ServiceProviderException('Create is handled by provisioning.', $operation),
            };

            $this->applyResult($service, $operation, $result->data);
            $operationRecord->update([
                'status' => 'completed',
                'completed_at' => now(),
                'response_metadata' => $result->data,
                'error_code' => null,
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            $operationRecord->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_code' => $e::class,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $service->fresh();
    }

    private function claimOperation(Service $service, ServiceProviderOperation $operation, string $key, ?Plan $plan, int $value): ServiceOperation
    {
        try {
            return DB::transaction(function () use ($service, $operation, $key, $plan, $value): ServiceOperation {
                $record = ServiceOperation::query()->where('idempotency_key', $key)->lockForUpdate()->first();

                if ($record === null) {
                    $record = ServiceOperation::query()->create([
                        'service_id' => $service->getKey(),
                        'operation' => $operation->value,
                        'status' => 'pending',
                        'service_provider_id' => $service->service_provider_id,
                        'provider_account_id' => $service->provider_account_id,
                        'idempotency_key' => $key,
                        'started_at' => now(),
                        'request_metadata' => array_filter([
                            'plan_id' => $plan?->getKey(),
                            'value' => $value ?: null,
                        ], static fn ($item): bool => $item !== null),
                    ]);
                }

                if ($record->status === 'failed') {
                    $record->update([
                        'status' => 'pending',
                        'started_at' => now(),
                        'completed_at' => null,
                        'error_code' => null,
                        'error_message' => null,
                    ]);
                }

                if ($record->status === 'pending') {
                    $record->update(['status' => 'running', 'started_at' => now()]);
                }

                return $record->fresh();
            });
        } catch (QueryException $e) {
            $record = ServiceOperation::query()->where('idempotency_key', $key)->first();
            if ($record !== null) {
                return $record;
            }
            throw $e;
        }
    }

    private function applyResult(Service $service, ServiceProviderOperation $operation, array $data): void
    {
        $updates = ['metadata' => [...($service->metadata ?? []), 'last_provider_result' => $data]];

        if (isset($data['capacity']) && is_numeric($data['capacity'])) {
            $updates['capacity'] = (int) $data['capacity'];
        }

        if (isset($data['expires_at']) && is_string($data['expires_at'])) {
            $updates['expires_at'] = $data['expires_at'];
        }

        if ($operation === ServiceProviderOperation::STATUS && isset($data['status'])) {
            $updates['status'] = $this->mapStatus((string) $data['status']);
        } elseif ($operation === ServiceProviderOperation::DISABLE) {
            $updates['status'] = ServiceStatus::Disabled->value;
        } elseif ($operation === ServiceProviderOperation::DELETE) {
            $updates['status'] = ServiceStatus::Disabled->value;
            $updates['metadata']['provider_deleted_at'] = now()->toISOString();
        } elseif (in_array($operation, [ServiceProviderOperation::RENEW, ServiceProviderOperation::EXTEND, ServiceProviderOperation::ADD_CAPACITY], true)) {
            $updates['status'] = ServiceStatus::Active->value;
        }

        $service->update($updates);
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'active', 'enabled' => ServiceStatus::Active->value,
            'disabled', 'inactive' => ServiceStatus::Disabled->value,
            'expired' => ServiceStatus::Expired->value,
            'suspended' => ServiceStatus::Suspended->value,
            default => ServiceStatus::Failed->value,
        };
    }

    private function defaultKey(Service $service, ServiceProviderOperation $operation, int $value, ?Plan $plan): string
    {
        return implode(':', [
            'service',
            $service->getKey(),
            $operation->value,
            $plan?->getKey() ?? 'none',
            $value ?: 'none',
        ]);
    }
}
