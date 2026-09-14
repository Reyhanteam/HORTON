<?php

namespace App\Models;

class Referral extends HortonModel
{
    protected $casts = ['qualified_at' => 'datetime', 'rewarded_at' => 'datetime'];

    public function referrer()
    {
        return $this->belongsTo(TelegramAccount::class, 'referrer_telegram_account_id');
    }

    public function referred()
    {
        return $this->belongsTo(TelegramAccount::class, 'referred_telegram_account_id');
    }
}
