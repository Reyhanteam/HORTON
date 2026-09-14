<?php

namespace App\Models;

class DiscountUsage extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
