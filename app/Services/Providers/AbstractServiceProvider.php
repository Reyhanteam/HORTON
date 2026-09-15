<?php

declare(strict_types=1);

namespace App\Services\Providers;

use App\Contracts\ServiceProviderContract;
use App\DTOs\ServiceProviderContext;
use App\DTOs\ServiceProviderResult;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\TelegramAccount;

abstract class AbstractServiceProvider implements ServiceProviderContract
{
    public function create(TelegramAccount $account, Plan $plan, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertCreate($account, $plan, $context);
        return $this->doCreate($account, $plan, $context);
    }

    public function get(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::GET);
        return $this->doGet($service, $context);
    }

    public function renew(Service $service, Plan $plan, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::RENEW);
        if ($plan->duration === null || (int) $plan->duration <= 0) {
            throw new ServiceProviderException('Plan duration must be positive.', ServiceProviderOperation::RENEW);
        }
        return $this->doRenew($service, $plan, $context);
    }

    public function extend(Service $service, int $days, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::EXTEND);
        if ($days <= 0) {
            throw new ServiceProviderException('Extension days must be positive.', ServiceProviderOperation::EXTEND);
        }
        return $this->doExtend($service, $days, $context);
    }

    public function addCapacity(Service $service, int $capacity, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::ADD_CAPACITY);
        if ($capacity <= 0) {
            throw new ServiceProviderException('Capacity increment must be positive.', ServiceProviderOperation::ADD_CAPACITY);
        }
        return $this->doAddCapacity($service, $capacity, $context);
    }

    public function disable(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::DISABLE);
        return $this->doDisable($service, $context);
    }

    public function delete(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::DELETE);
        return $this->doDelete($service, $context);
    }

    public function status(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult
    {
        $context = $this->normalizeContext($context);
        $this->assertService($service, $context, ServiceProviderOperation::STATUS);
        return $this->doStatus($service, $context);
    }

    abstract protected function doCreate(TelegramAccount $account, Plan $plan, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doGet(Service $service, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doRenew(Service $service, Plan $plan, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doExtend(Service $service, int $days, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doAddCapacity(Service $service, int $capacity, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doDisable(Service $service, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doDelete(Service $service, ServiceProviderContext $context): ServiceProviderResult;
    abstract protected function doStatus(Service $service, ServiceProviderContext $context): ServiceProviderResult;

    protected function normalizeContext(?ServiceProviderContext $context): ServiceProviderContext
    {
        return $context ?? new ServiceProviderContext;
    }

    protected function assertCreate(TelegramAccount $account, Plan $plan, ServiceProviderContext $context): void
    {
        if (! $account->exists || ! $plan->exists) {
            throw new ServiceProviderException('Provider creation requires persisted account and plan.', ServiceProviderOperation::CREATE);
        }
        if ($context->provider?->exists === false) {
            throw new ServiceProviderException('Provider instance is invalid.', ServiceProviderOperation::CREATE);
        }
        if ($context->account?->exists === false) {
            throw new ServiceProviderException('Provider account is invalid.', ServiceProviderOperation::CREATE);
        }
        if ($context->account && $context->provider && (int) $context->account->service_provider_id !== (int) $context->provider->id) {
            throw new ServiceProviderException('Provider account does not belong to provider instance.', ServiceProviderOperation::CREATE);
        }
    }

    protected function assertService(Service $service, ServiceProviderContext $context, ServiceProviderOperation $operation): void
    {
        if (! $service->exists) {
            throw new ServiceProviderException('Provider operation requires a persisted service.', $operation);
        }
        if ($context->provider && (int) $service->service_provider_id !== (int) $context->provider->id) {
            throw new ServiceProviderException('Service does not belong to selected provider instance.', $operation);
        }
        if ($context->account && $service->provider_account_id !== null && (int) $service->provider_account_id !== (int) $context->account->id) {
            throw new ServiceProviderException('Service does not belong to selected provider account.', $operation);
        }
    }
}
