<?php

namespace App\Models;

class Plan extends HortonModel
{
    protected $casts = ['duration' => 'integer', 'capacity' => 'integer', 'limits' => 'array', 'metadata' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
