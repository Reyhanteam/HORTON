<?php

namespace App\Models;

class Notification extends HortonModel
{
    protected $casts = ['data' => 'array', 'read_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function deliveries()
    {
        return $this->hasMany(NotificationDelivery::class);
    }
}
