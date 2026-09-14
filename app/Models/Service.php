<?php

namespace App\Models;

class Service extends HortonModel
{
    protected $casts = ['capacity' => 'integer', 'starts_at' => 'datetime', 'expires_at' => 'datetime', 'metadata' => 'array'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function providerAccount()
    {
        return $this->belongsTo(ServiceProviderAccount::class, 'service_provider_account_id');
    }

    public function operations()
    {
        return $this->hasMany(ServiceOperation::class);
    }
}
