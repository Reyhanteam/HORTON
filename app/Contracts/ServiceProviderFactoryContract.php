<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\ServiceProvider;

interface ServiceProviderFactoryContract
{
    public function make(ServiceProvider $provider): ServiceProviderContract;
}
