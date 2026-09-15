<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;

final readonly class ProviderSelectionResult
{
    public function __construct(
        public ServiceProvider $provider,
        public ?ServiceProviderAccount $account = null,
    ) {}
}
