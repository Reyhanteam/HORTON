<?php

namespace App\Models;

class TelegramAccount extends HortonModel
{
    protected $casts = [
        'last_seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function cashbackAccounts()
    {
        return $this->hasMany(CashbackAccount::class);
    }

    public function referralAccount()
    {
        return $this->hasOne(ReferralAccount::class);
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_telegram_account_id');
    }

    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referred_telegram_account_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function discountUsages()
    {
        return $this->hasMany(DiscountUsage::class);
    }

    public function giftCodeRedemptions()
    {
        return $this->hasMany(GiftCodeRedemption::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function cashbackTransactions()
    {
        return $this->hasMany(CashbackTransaction::class);
    }

    public function broadcastRecipients()
    {
        return $this->hasMany(BroadcastRecipient::class);
    }
}
