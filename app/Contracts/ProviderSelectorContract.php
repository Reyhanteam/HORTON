<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\ServiceProviderOperation;
use App\Models\Plan;
use App\Models\ServiceProvider;
use App\Models\TelegramAccount;
use App\DTOs\ProviderSelectionResult;

interface ProviderSelectorContract
{
    public function select(
        TelegramAccount $account,
        Plan $plan,
        ServiceProviderOperation $operation = ServiceProviderOperation::CREATE,
        ?string $region = null,
    ): ProviderSelectionResult;
}
