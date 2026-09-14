<?php

namespace App\Models;

class CashbackTransaction extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'balance_before' => 'integer', 'balance_after' => 'integer', 'metadata' => 'array'];

    public function account()
    {
        return $this->belongsTo(CashbackAccount::class, 'cashback_account_id');
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }
}
