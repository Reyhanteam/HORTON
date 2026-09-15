<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;

interface ServiceProviderContract
{
    public function create(TelegramAccount $account, Plan $plan, array $context = []): array;
    public function get(Service $service): array;
    public function renew(Service $service, Plan $plan): array;
    public function extend(Service $service, int $days): array;
    public function addCapacity(Service $service, int $capacity): array;
    public function disable(Service $service): array;
    public function delete(Service $service): array;
    public function status(Service $service): array;
}
