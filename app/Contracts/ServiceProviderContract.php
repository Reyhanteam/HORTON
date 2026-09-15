<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\ServiceProviderContext;
use App\DTOs\ServiceProviderResult;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;

interface ServiceProviderContract
{
    public function create(TelegramAccount $account, Plan $plan, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function get(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function renew(Service $service, Plan $plan, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function extend(Service $service, int $days, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function addCapacity(Service $service, int $capacity, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function disable(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function delete(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult;
    public function status(Service $service, ?ServiceProviderContext $context = null): ServiceProviderResult;
}
