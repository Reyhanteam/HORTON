<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ExecuteServiceProviderOperation;
use App\Enums\ServiceProviderOperation;
use App\Models\Plan;
use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ExecuteServiceProviderOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public array $backoff = [10, 60, 180];

    public function __construct(
        public readonly int $serviceId,
        public readonly ServiceProviderOperation $operation,
        public readonly ?int $planId = null,
        public readonly int $value = 0,
        public readonly ?string $idempotencyKey = null,
    ) {
        $this->onQueue('providers');
    }

    public function handle(ExecuteServiceProviderOperation $action): void
    {
        $service = Service::query()->findOrFail($this->serviceId);
        $plan = $this->planId === null ? null : Plan::query()->findOrFail($this->planId);

        $action->execute($service, $this->operation, $plan, $this->value, $this->idempotencyKey);
    }
}
