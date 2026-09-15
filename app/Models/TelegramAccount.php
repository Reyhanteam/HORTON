<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Support\Carbon;

class TelegramAccount extends HortonModel
{
    protected $casts = [
        'last_seen_at' => 'datetime',
        'registered_at' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function orders() { return $this->hasMany(Order::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function wallets() { return $this->hasMany(Wallet::class); }
    public function cashbackAccounts() { return $this->hasMany(CashbackAccount::class); }
    public function referralAccount() { return $this->hasOne(ReferralAccount::class); }
    public function referralsMade() { return $this->hasMany(Referral::class, 'referrer_telegram_account_id'); }
    public function referralsReceived() { return $this->hasMany(Referral::class, 'referred_telegram_account_id'); }
    public function services() { return $this->hasMany(Service::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    public function discountUsages() { return $this->hasMany(DiscountUsage::class); }
    public function giftCodeRedemptions() { return $this->hasMany(GiftCodeRedemption::class); }
    public function walletTransactions() { return $this->hasMany(WalletTransaction::class); }
    public function cashbackTransactions() { return $this->hasMany(CashbackTransaction::class); }
    public function broadcastRecipients() { return $this->hasMany(BroadcastRecipient::class); }

    public function isRegistered(): bool
    {
        return in_array($this->registration_status ?? 'pending', ['registered', 'active'], true);
    }

    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? false) && $this->registration_status === 'active';
    }

    public function canUseBot(): bool { return $this->isActive(); }

    public function markRegistered(?Carbon $at = null): void
    {
        $at ??= now();
        $this->forceFill(['registration_status' => 'registered', 'registered_at' => $this->registered_at ?? $at])->save();
    }

    public function activate(?Carbon $at = null): void
    {
        $at ??= now();
        $this->forceFill(['registration_status' => 'active', 'is_active' => true, 'activated_at' => $this->activated_at ?? $at, 'deactivated_at' => null])->save();
    }

    public function deactivate(?Carbon $at = null): void
    {
        $at ??= now();
        $this->forceFill(['registration_status' => 'inactive', 'is_active' => false, 'deactivated_at' => $at])->save();
    }

    public function status(): UserStatus
    {
        return ($this->is_active ?? false) ? UserStatus::Active : UserStatus::Inactive;
    }
}
