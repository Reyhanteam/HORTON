<?php

namespace App\Models;

class BroadcastRecipient extends HortonModel
{
    protected $casts = ['attempts' => 'integer', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }
}
