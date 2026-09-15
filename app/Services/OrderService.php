<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateOrderData;
use App\Exceptions\DomainRuleViolation;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class OrderService
{
    public function create(CreateOrderData $data): Order
    {
        if ($data->quantity < 1) {
            throw new DomainRuleViolation('Order quantity must be positive.');
        }

        return DB::transaction(function () use ($data): Order {
            $account = TelegramAccount::query()->findOrFail($data->telegramAccountId);
            $plan = Plan::query()->with('product')->findOrFail($data->planId);

            if (! $account->canUseBot()) {
                throw new DomainRuleViolation('Inactive Telegram accounts cannot create orders.');
            }

            $subtotal = $data->subtotal();
            $total = $data->total();

            $order = Order::query()->create([
                'uuid' => (string) Str::uuid(),
                'telegram_account_id' => $account->getKey(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $data->discountAmount,
                'total' => $total,
                'metadata' => $data->metadata,
            ]);

            $order->items()->create([
                'product_id' => $plan->product_id,
                'plan_id' => $plan->getKey(),
                'quantity' => $data->quantity,
                'unit_price' => $data->unitPrice,
                'subtotal' => $subtotal,
                'total' => $total,
                'snapshot' => [
                    'plan_id' => $plan->getKey(),
                    'product_id' => $plan->product_id,
                    'duration' => $plan->duration,
                    'capacity' => $plan->capacity,
                    'limits' => $plan->limits,
                ],
                'metadata' => ['discount_code' => $data->discountCode],
            ]);

            Invoice::query()->create([
                'order_id' => $order->getKey(),
                'invoice_number' => 'INV-'.strtoupper(Str::random(12)),
                'status' => 'issued',
                'subtotal' => $subtotal,
                'discount_amount' => $data->discountAmount,
                'total' => $total,
                'issued_at' => now(),
            ]);

            return $order->fresh(['items', 'invoice']);
        });
    }

    public function markPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $locked = Order::query()->whereKey($order->getKey())->lockForUpdate()->firstOrFail();
            if ($locked->status === 'paid') {
                return $locked;
            }
            if ($locked->status !== 'pending') {
                throw new DomainRuleViolation('Only pending orders can be marked as paid.');
            }

            $locked->forceFill(['status' => 'paid', 'paid_at' => now()])->save();
            $locked->invoice()->update(['status' => 'paid', 'paid_at' => now()]);
            return $locked->fresh(['items', 'invoice']);
        });
    }
}
