<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\ServiceProviderContract;
use App\Exceptions\ServiceProviderException;
use App\Models\ServiceProvider;
use App\Services\Providers\FakeServiceProvider;
use App\Services\Providers\ServiceProviderFactory;
use Tests\TestCase;

final class ServiceProviderFactoryTest extends TestCase
{
    public function test_fake_driver_resolves_through_container(): void
    {
        $provider = new ServiceProvider(['driver' => 'fake']);
        $instance = app(ServiceProviderFactory::class)->make($provider);
        $this->assertInstanceOf(ServiceProviderContract::class, $instance);
        $this->assertInstanceOf(FakeServiceProvider::class, $instance);
    }

    public function test_unknown_driver_fails_closed(): void
    {
        $provider = new ServiceProvider(['driver' => 'unknown-driver']);
        $this->expectException(ServiceProviderException::class);
        app(ServiceProviderFactory::class)->make($provider);
    }

    public function test_factory_rejects_configured_class_that_does_not_implement_contract(): void
    {
        config(['horton.providers.drivers.invalid' => \stdClass::class]);
        $provider = new ServiceProvider(['driver' => 'invalid']);
        $this->expectException(ServiceProviderException::class);
        app(ServiceProviderFactory::class)->make($provider);
    }
}
