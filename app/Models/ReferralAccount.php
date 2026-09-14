<?php

namespace App\Models;

class ReferralAccount extends HortonModel
{
    protected $casts = ['commission_rate' => 'decimal:2', 'cashback_rate' => 'decimal:2', 'is_active' => 'boolean'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }
}
