<?php

declare(strict_types=1);

namespace App\Services\Providers;

use App\DTOs\ServiceProviderContext;
use App\DTOs\ServiceProviderResult;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;
use Illuminate\Support\Str;

final class FakeServiceProvider extends AbstractServiceProvider
{
    private array $failures = [];
    private array $calls = [];
    private array $idempotentResults = [];

    public function fail(ServiceProviderOperation $operation, string $message = 'Simulated provider failure.'): self
    {
        $this->failures[$operation->value] = $message;
        return $this;
    }

    public function calls(): array { return $this->calls; }

    protected function doCreate(TelegramAccount $account, Plan $plan, ServiceProviderContext $context): ServiceProviderResult
    {
        return $this->result(ServiceProviderOperation::CREATE, $context, [
            'account_id' => $account->getKey(),
            'plan_id' => $plan->getKey(),
            'capacity' => (int) $plan->capacity,
            'status' => 'active',
            'inbounds' => $context->inbounds,
        ]);
    }

    protected function doGet(Service $service, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::GET, $context, ['status' => $service->status, 'capacity' => (int) $service->capacity]); }

    protected function doRenew(Service $service, Plan $plan, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::RENEW, $context, ['status' => 'active', 'expires_at' => now()->addDays((int) $plan->duration)->toISOString()]); }

    protected function doExtend(Service $service, int $days, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::EXTEND, $context, ['expires_at' => ($service->expires_at ?? now())->addDays($days)->toISOString()]); }

    protected function doAddCapacity(Service $service, int $capacity, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::ADD_CAPACITY, $context, ['capacity' => (int) $service->capacity + $capacity]); }

    protected function doDisable(Service $service, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::DISABLE, $context, ['status' => 'disabled']); }

    protected function doDelete(Service $service, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::DELETE, $context, ['status' => 'deleted']); }

    protected function doStatus(Service $service, ServiceProviderContext $context): ServiceProviderResult
    { return $this->result(ServiceProviderOperation::STATUS, $context, ['status' => $service->status, 'capacity' => (int) $service->capacity]); }

    private function result(ServiceProviderOperation $operation, ServiceProviderContext $context, array $data): ServiceProviderResult
    {
        $key = $context->idempotencyKey;
        $this->calls[] = ['operation' => $operation->value, 'key' => $key];

        // Idempotency is scoped to an operation. The same business idempotency
        // key may legitimately be reused across different lifecycle operations.
        $cacheKey = $key === null ? null : $operation->value.':'.$key;
        if ($cacheKey !== null && isset($this->idempotentResults[$cacheKey])) {
            return $this->idempotentResults[$cacheKey];
        }

        if (isset($this->failures[$operation->value])) {
            $message = $this->failures[$operation->value];
            unset($this->failures[$operation->value]);
            throw new ServiceProviderException($message, $operation, false, ['idempotency_key' => $key]);
        }

        $result = ServiceProviderResult::success(
            $operation,
            $data,
            'fake-'.Str::uuid(),
            $key,
        );
        if ($cacheKey !== null) {
            $this->idempotentResults[$cacheKey] = $result;
        }
        return $result;
    }
}
