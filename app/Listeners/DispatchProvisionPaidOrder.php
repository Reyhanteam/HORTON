<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Jobs\ProvisionPaidOrderJob;

final class DispatchProvisionPaidOrder
{
    public function handle(OrderPaid $event): void
    {
        ProvisionPaidOrderJob::dispatch($event->order->getKey());
    }
}
