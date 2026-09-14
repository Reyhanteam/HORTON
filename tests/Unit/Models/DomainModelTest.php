<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\ServiceOperation;
use App\Models\User;
use Tests\TestCase;

final class DomainModelTest extends TestCase
{
    public function test_core_order_relations_are_defined(): void
    {
        $order = new Order();

        self::assertSame(User::class, $order->user()->getRelated()::class);
        self::assertSame(OrderItem::class, $order->items()->getRelated()::class);
        self::assertSame(Service::class, $order->services()->getRelated()::class);
    }

    public function test_core_service_relations_are_defined(): void
    {
        $service = new Service();

        self::assertSame(User::class, $service->user()->getRelated()::class);
        self::assertSame(Order::class, $service->order()->getRelated()::class);
        self::assertSame(OrderItem::class, $service->orderItem()->getRelated()::class);
        self::assertSame(ServiceProvider::class, $service->provider()->getRelated()::class);
        self::assertSame(ServiceProviderAccount::class, $service->providerAccount()->getRelated()::class);
        self::assertSame(ServiceOperation::class, $service->operations()->getRelated()::class);
    }
}
