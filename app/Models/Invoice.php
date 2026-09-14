<?php

namespace App\Models;

class Invoice extends HortonModel
{
    protected $casts = ['subtotal' => 'integer', 'discount_amount' => 'integer', 'total' => 'integer', 'issued_at' => 'datetime', 'paid_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
