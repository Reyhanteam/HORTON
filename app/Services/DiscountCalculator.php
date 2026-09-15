<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountService;
use App\DTOs\DiscountResult;
use App\Exceptions\DomainRuleViolation;
use App\Models\DiscountCode;
use App\Models\Plan;
use App\Models\TelegramAccount;

final class DiscountCalculator implements DiscountService
{
    public function calculate(string $code, TelegramAccount $account, Plan $plan, int $subtotal): DiscountResult
    {
        $discount = DiscountCode::query()
            ->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))])
            ->where('is_active', true)
            ->first();

        if (! $discount || ($discount->starts_at && $discount->starts_at->isFuture()) || ($discount->ends_at && $discount->ends_at->isPast())) {
            throw new DomainRuleViolation('Discount code is invalid or expired.');
        }

        if ($subtotal < (int) $discount->minimum_order_amount) {
            throw new DomainRuleViolation('Order total does not meet the discount minimum.');
        }

        if ($discount->usage_limit !== null && $discount->usages()->count() >= $discount->usage_limit) {
            throw new DomainRuleViolation('Discount usage limit has been reached.');
        }

        if ($discount->per_user_limit !== null && $discount->usages()->where('telegram_account_id', $account->getKey())->count() >= $discount->per_user_limit) {
            throw new DomainRuleViolation('You have reached the usage limit for this discount.');
        }

        $amount = $discount->type === 'percentage'
            ? (int) floor($subtotal * ((int) $discount->value) / 100)
            : (int) $discount->value;

        if ($discount->maximum_discount_amount !== null) {
            $amount = min($amount, (int) $discount->maximum_discount_amount);
        }

        $amount = min(max(0, $amount), max(0, $subtotal));

        return new DiscountResult((int) $discount->getKey(), $amount, $subtotal - $amount, $code);
    }
}
