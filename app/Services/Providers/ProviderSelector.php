<?php

declare(strict_types=1);

namespace App\Services\Providers;

use App\Contracts\ProviderSelectorContract;
use App\DTOs\ProviderSelectionResult;
use App\Enums\ServiceProviderOperation;
use App\Exceptions\ServiceProviderException;
use App\Models\Plan;
use App\Models\ServiceProvider;
use App\Models\TelegramAccount;

final class ProviderSelector implements ProviderSelectorContract
{
    public function select(TelegramAccount $account, Plan $plan, ServiceProviderOperation $operation = ServiceProviderOperation::CREATE, ?string $region = null): ProviderSelectionResult
    {
        $providers = ServiceProvider::query()->where('status', 'active')->when($region !== null, fn ($q) => $q->where('region', $region))->orderBy('priority')->orderBy('id')->get();
        foreach ($providers as $provider) {
            if (! $this->isHealthyEnough($provider) || ! $this->supports($provider, $operation) || ! $this->withinCapacity($provider) || ! $this->withinPolicy($provider, $operation, $account, $plan)) continue;
            return new ProviderSelectionResult($provider, $provider->accounts()->where('status', 'active')->orderBy('priority')->orderBy('id')->first());
        }
        throw new ServiceProviderException('No eligible service provider is currently available.', $operation, true, ['region' => $region, 'plan_id' => $plan->getKey()]);
    }

    private function isHealthyEnough(ServiceProvider $provider): bool { return in_array($provider->health_status ?? 'unknown', ['unknown','online','slow'], true); }
    private function supports(ServiceProvider $provider, ServiceProviderOperation $operation): bool { $capability=$provider->capabilities()->where('operation',$operation->value)->first(); return $capability === null || (bool) $capability->supported; }

    private function withinCapacity(ServiceProvider $provider): bool
    {
        $active = ['pending','active','suspended'];
        if ($provider->max_services !== null && $provider->services()->whereIn('status',$active)->count() >= (int) $provider->max_services) return false;
        if ($provider->max_users !== null && $provider->services()->whereIn('status',$active)->distinct('telegram_account_id')->count('telegram_account_id') >= (int) $provider->max_users) return false;
        return true;
    }

    private function withinPolicy(ServiceProvider $provider, ServiceProviderOperation $operation, TelegramAccount $account, Plan $plan): bool
    {
        $policy=$provider->policy; if ($policy === null) return true;
        $allowed=match($operation){
            ServiceProviderOperation::CREATE => (bool)$policy->allow_create && (! $plan->is_trial || (bool)$policy->allow_trial),
            ServiceProviderOperation::RENEW => (bool)$policy->allow_renew,
            ServiceProviderOperation::EXTEND => (bool)$policy->allow_extend,
            ServiceProviderOperation::ADD_CAPACITY => (bool)$policy->allow_add_capacity,
            ServiceProviderOperation::DISABLE => (bool)$policy->allow_disable,
            ServiceProviderOperation::DELETE => (bool)$policy->allow_delete,
            default => true,
        };
        if (! $allowed) return false;
        if ($policy->max_capacity_per_service !== null && (int)$plan->capacity > (int)$policy->max_capacity_per_service) return false;
        if ($policy->max_duration_days !== null && (int)$plan->duration > (int)$policy->max_duration_days) return false;
        return $policy->max_services_per_user === null || $provider->services()->whereIn('status',['pending','active','suspended'])->where('telegram_account_id',$account->getKey())->count() < (int)$policy->max_services_per_user;
    }
}
