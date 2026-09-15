<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\CashbackAccount;
use App\Models\ReferralAccount;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

final class InitializeUserAccount implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered $event): void
    {
        $account = $event->account;

        Wallet::query()->firstOrCreate(
            ['telegram_account_id' => $account->getKey()],
            ['balance' => 0],
        );

        CashbackAccount::query()->firstOrCreate(
            ['telegram_account_id' => $account->getKey()],
            ['balance' => 0],
        );

        ReferralAccount::query()->firstOrCreate(
            ['telegram_account_id' => $account->getKey()],
            ['commission_rate' => 0, 'cashback_rate' => 0, 'is_active' => true],
        );
    }
}
