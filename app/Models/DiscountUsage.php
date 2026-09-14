<?php

namespace App\Models;

class DiscountUsage extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
