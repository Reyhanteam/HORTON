<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ProvisionPaidOrder;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProvisionPaidOrderJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $timeout = 120;
    public array $backoff = [10, 30, 60, 120];
    public int $uniqueFor = 900;

    public function __construct(public readonly int $orderId) {}

    public function uniqueId(): string { return 'provision-order:'.$this->orderId; }

    public function handle(ProvisionPaidOrder $provision): void
    {
        $provision->execute(Order::query()->findOrFail($this->orderId));
    }
}
