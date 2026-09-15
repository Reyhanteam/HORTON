<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Events\OrderPaid;
use App\Jobs\ProvisionPaidOrderJob;
use App\Listeners\DispatchProvisionPaidOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class ProvisioningQueueTest extends TestCase
{
    public function test_paid_order_listener_dispatches_unique_provisioning_job(): void
    {
        Queue::fake();
        $order = new Order(['id' => 123]);
        (new DispatchProvisionPaidOrder)->handle(new OrderPaid($order));
        Queue::assertPushed(ProvisionPaidOrderJob::class, fn (ProvisionPaidOrderJob $job): bool => $job->orderId === 123 && $job->uniqueId() === 'provision-order:123');
    }

    public function test_job_has_conservative_retry_configuration(): void
    {
        $job = new ProvisionPaidOrderJob(7);
        $this->assertSame(5, $job->tries);
        $this->assertSame(120, $job->timeout);
        $this->assertSame([10, 30, 60, 120], $job->backoff);
        $this->assertSame(900, $job->uniqueFor);
    }
}
