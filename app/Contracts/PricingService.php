<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Plan;
use App\Models\TelegramAccount;

interface PricingService
{
    public function priceFor(Plan $plan, ?TelegramAccount $account = null): int;
}
