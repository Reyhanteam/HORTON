<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\DiscountResult;
use App\Models\Plan;
use App\Models\TelegramAccount;

interface DiscountService
{
    public function calculate(string $code, TelegramAccount $account, Plan $plan, int $subtotal): DiscountResult;
}
