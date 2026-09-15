<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\DiscountService;
use App\Contracts\PaymentGateway;
use App\Contracts\PricingService;
use App\Contracts\ServiceProviderContract;
use App\Services\CatalogPricingService;
use App\Services\DiscountCalculator;
use App\Services\Payments\FakePaymentGateway;
use App\Services\Providers\FakeServiceProvider;
use Tests\TestCase;

final class Card08BindingsTest extends TestCase
{
    public function test_application_contracts_are_bound_to_replaceable_implementations(): void
    {
        self::assertInstanceOf(CatalogPricingService::class, app(PricingService::class));
        self::assertInstanceOf(DiscountCalculator::class, app(DiscountService::class));
        self::assertInstanceOf(FakePaymentGateway::class, app(PaymentGateway::class));
        self::assertInstanceOf(FakeServiceProvider::class, app(ServiceProviderContract::class));
    }
}
