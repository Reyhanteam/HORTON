<?php

declare(strict_types=1);

namespace App\Services\Providers;

use App\Contracts\ServiceProviderContract;
use App\Models\Plan;
use App\Models\Service;
use App\Models\TelegramAccount;
use Illuminate\Support\Str;

final class FakeServiceProvider implements ServiceProviderContract
{
    public function create(TelegramAccount $account, Plan $plan, array $context = []): array
    {
        return ['provider_id' => 'fake-'.Str::uuid(), 'status' => 'active', 'capacity' => $plan->capacity, 'metadata' => $context];
    }

    public function get(Service $service): array { return ['status' => $service->status, 'capacity' => $service->capacity]; }

    public function renew(Service $service, Plan $plan): array { return ['status' => 'active', 'expires_at' => now()->addDays((int) $plan->duration)->toISOString()]; }

    public function extend(Service $service, int $days): array { return ['expires_at' => ($service->expires_at ?? now())->addDays($days)->toISOString()]; }

    public function addCapacity(Service $service, int $capacity): array { return ['capacity' => (int) $service->capacity + $capacity]; }

    public function disable(Service $service): array { return ['status' => 'disabled']; }

    public function delete(Service $service): array { return ['status' => 'deleted']; }

    public function status(Service $service): array { return ['status' => $service->status, 'capacity' => $service->capacity]; }
}
