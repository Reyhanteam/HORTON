<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\CreateOrderData;
use App\DTOs\PaymentRequest;
use App\DTOs\ServiceProviderContext;
use App\Enums\ServiceProviderOperation;
use App\Services\Payments\FakePaymentGateway;
use App\Services\Providers\FakeServiceProvider;
use App\Models\Plan;
use App\Models\TelegramAccount;
use PHPUnit\Framework\TestCase;

final class Card08ApplicationServicesTest extends TestCase
{
    public function test_order_dto_calculates_subtotal_and_total(): void
    {
        $data = new CreateOrderData(10, 20, 1500, 3, 500);
        self::assertSame(4500, $data->subtotal());
        self::assertSame(4000, $data->total());
    }

    public function test_fake_payment_gateway_has_stable_contract_shape(): void
    {
        $result = (new FakePaymentGateway())->verify(new PaymentRequest(7, 1000), 'auth-7');
        self::assertSame('success', $result->status);
        self::assertSame('auth-7', $result->authority);
        self::assertSame('fake-ref-7', $result->reference);
    }

    public function test_fake_service_provider_uses_the_card_09_contract(): void
    {
        $account = new TelegramAccount();
        $account->exists = true;
        $account->id = 1;
        $plan = new Plan(['duration_value' => 30, 'capacity_value' => 10]);
        $plan->exists = true;
        $plan->id = 2;

        $result = (new FakeServiceProvider())->create($account, $plan, new ServiceProviderContext(null, null, 'card-09'));

        self::assertTrue($result->success);
        self::assertSame(ServiceProviderOperation::CREATE, $result->operation);
        self::assertSame('active', $result->data['status']);
        self::assertSame(10, $result->data['capacity']);
        self::assertStringStartsWith('fake-', $result->externalId);
    }
}
