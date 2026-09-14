<?php

namespace App\Models;

class CashbackAccount extends HortonModel
{
    protected $casts = ['balance' => 'integer'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function transactions()
    {
        return $this->hasMany(CashbackTransaction::class);
    }
}
