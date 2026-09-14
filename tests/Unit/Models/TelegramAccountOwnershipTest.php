<?php

namespace Tests\Unit\Models;

use App\Models\BroadcastRecipient;
use App\Models\CashbackAccount;
use App\Models\CashbackTransaction;
use App\Models\DiscountUsage;
use App\Models\GiftCodeRedemption;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\ReferralAccount;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\TelegramAccount;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use PHPUnit\Framework\TestCase;

class TelegramAccountOwnershipTest extends TestCase
{
    public function test_telegram_account_owns_telegram_user_business_models(): void
    {
        $models = [
            new Order(),
            new Payment(),
            new Wallet(),
            new CashbackAccount(),
            new ReferralAccount(),
            new Referral(),
            new Service(),
            new Notification(),
            new SupportTicket(),
            new DiscountUsage(),
            new GiftCodeRedemption(),
            new WalletTransaction(),
            new CashbackTransaction(),
            new BroadcastRecipient(),
        ];

        foreach ($models as $model) {
            $relation = match (true) {
                $model instanceof Referral => $model->referrer(),
                default => $model->telegramAccount(),
            };

            $this->assertInstanceOf(TelegramAccount::class, $relation->getRelated());
        }
    }

    public function test_referral_has_both_telegram_account_owners(): void
    {
        $referral = new Referral();

        $this->assertInstanceOf(TelegramAccount::class, $referral->referrer()->getRelated());
        $this->assertInstanceOf(TelegramAccount::class, $referral->referred()->getRelated());
    }

    public function test_dashboard_user_has_no_telegram_business_relationships(): void
    {
        $forbidden = [
            'telegramAccounts',
            'orders',
            'payments',
            'wallets',
            'cashbackAccounts',
            'referralAccount',
            'referralsMade',
            'referralsReceived',
            'services',
            'notifications',
            'supportTickets',
            'discountUsages',
            'giftCodeRedemptions',
            'walletTransactions',
            'cashbackTransactions',
        ];

        foreach ($forbidden as $method) {
            $this->assertFalse(method_exists(User::class, $method), "User must not expose {$method}().");
        }
    }
}
