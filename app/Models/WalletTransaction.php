<?php

namespace App\Models;

class WalletTransaction extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'balance_before' => 'integer', 'balance_after' => 'integer', 'metadata' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }
}
