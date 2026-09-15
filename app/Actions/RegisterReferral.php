<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\DomainRuleViolation;
use App\Models\Referral;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;

final class RegisterReferral
{
    public function execute(TelegramAccount $referrer, TelegramAccount $referred, ?string $source = null): Referral
    {
        if ($referrer->is($referred)) {
            throw new DomainRuleViolation('A user cannot refer themselves.');
        }

        return DB::transaction(function () use ($referrer, $referred, $source): Referral {
            $existing = Referral::query()->where('referred_telegram_account_id', $referred->getKey())->lockForUpdate()->first();
            if ($existing) return $existing;

            return Referral::query()->create([
                'referrer_telegram_account_id' => $referrer->getKey(),
                'referred_telegram_account_id' => $referred->getKey(),
                'source' => $source,
                'status' => 'registered',
            ]);
        });
    }
}
