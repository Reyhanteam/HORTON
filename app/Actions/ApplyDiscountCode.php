<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\DiscountService;
use App\DTOs\DiscountResult;
use App\Exceptions\DomainRuleViolation;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\Plan;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;

final class ApplyDiscountCode
{
    public function __construct(private readonly DiscountService $discounts) {}

    public function preview(string $code, TelegramAccount $account, Plan $plan, int $subtotal): DiscountResult
    {
        return $this->discounts->calculate($code, $account, $plan, $subtotal);
    }

    public function record(string $code, TelegramAccount $account, Plan $plan, Order $order, int $subtotal): DiscountResult
    {
        return DB::transaction(function () use ($code, $account, $plan, $order, $subtotal): DiscountResult {
            $result = $this->discounts->calculate($code, $account, $plan, $subtotal);

            if (DiscountUsage::query()->where('order_id', $order->getKey())->exists()) {
                throw new DomainRuleViolation('A discount has already been recorded for this order.');
            }

            DiscountUsage::query()->create([
                'discount_code_id' => $result->discountCodeId,
                'telegram_account_id' => $account->getKey(),
                'order_id' => $order->getKey(),
                'amount' => $result->amount,
            ]);

            return $result;
        });
    }
}
