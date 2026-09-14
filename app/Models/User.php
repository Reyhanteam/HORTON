<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function telegramAccounts()
    {
        return $this->hasMany(TelegramAccount::class);
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
        return $this->hasMany(Referral::class, 'referrer_user_id');
    }

    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referred_user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
}
