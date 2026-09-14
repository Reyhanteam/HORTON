<?php

namespace App\Models;

class DiscountCode extends HortonModel
{
    protected $casts = ['value' => 'integer', 'minimum_order_amount' => 'integer', 'maximum_discount_amount' => 'integer', 'usage_limit' => 'integer', 'per_user_limit' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];

    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
