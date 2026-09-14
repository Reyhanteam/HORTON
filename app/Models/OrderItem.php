<?php

namespace App\Models;

class OrderItem extends HortonModel
{
    protected $casts = ['quantity' => 'integer', 'unit_price' => 'integer', 'subtotal' => 'integer', 'total' => 'integer', 'snapshot' => 'array', 'metadata' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }
}
