<?php

namespace App\Models;

class Payment extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'metadata' => 'array', 'paid_at' => 'datetime', 'failed_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function attempts()
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function callbacks()
    {
        return $this->hasMany(PaymentCallback::class);
    }
}
