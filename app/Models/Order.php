<?php

namespace App\Models;

class Order extends HortonModel
{
    protected $casts = ['subtotal' => 'integer', 'discount_amount' => 'integer', 'total' => 'integer', 'metadata' => 'array', 'paid_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function discountUsages()
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
