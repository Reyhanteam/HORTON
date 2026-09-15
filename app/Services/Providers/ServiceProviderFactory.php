<?php

declare(strict_types=1);

namespace App\Services\Providers;

use App\Contracts\ServiceProviderContract;
use App\Exceptions\ServiceProviderException;
use App\Models\ServiceProvider;

final class ServiceProviderFactory
{
    public function make(ServiceProvider $provider): ServiceProviderContract
    {
        $class = config('horton.providers.drivers.'.$provider->driver);
        if (! is_string($class) || $class === '') {
            throw new ServiceProviderException("No provider driver is registered for [{$provider->driver}].");
        }

        $instance = app()->make($class);
        if (! $instance instanceof ServiceProviderContract) {
            throw new ServiceProviderException("Provider driver [{$provider->driver}] must implement ServiceProviderContract.");
        }
        return $instance;
    }
}
