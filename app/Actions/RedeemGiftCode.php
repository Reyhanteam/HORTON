<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\DomainRuleViolation;
use App\Models\GiftCode;
use App\Models\GiftCodeRedemption;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;

final class RedeemGiftCode
{
    public function execute(string $code, TelegramAccount $account, ?int $orderId = null): GiftCodeRedemption
    {
        return DB::transaction(function () use ($code, $account, $orderId): GiftCodeRedemption {
            $gift = GiftCode::query()->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))])->lockForUpdate()->first();
            if (! $gift || ! $gift->is_active || ($gift->starts_at && $gift->starts_at->isFuture()) || ($gift->ends_at && $gift->ends_at->isPast())) {
                throw new DomainRuleViolation('Gift code is invalid or expired.');
            }

            if ($gift->usage_limit !== null && $gift->redemptions()->count() >= $gift->usage_limit) {
                throw new DomainRuleViolation('Gift code usage limit has been reached.');
            }

            if ($gift->per_user_limit !== null && $gift->redemptions()->where('telegram_account_id', $account->getKey())->count() >= $gift->per_user_limit) {
                throw new DomainRuleViolation('You have already used this gift code.');
            }

            return GiftCodeRedemption::query()->create([
                'gift_code_id' => $gift->getKey(),
                'telegram_account_id' => $account->getKey(),
                'order_id' => $orderId,
                'value' => (int) $gift->value,
            ]);
        });
    }
}
