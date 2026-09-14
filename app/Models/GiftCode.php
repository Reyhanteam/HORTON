<?php

namespace App\Models;

class GiftCode extends HortonModel
{
    protected $casts = ['value' => 'integer', 'usage_limit' => 'integer', 'per_user_limit' => 'integer', 'minimum_order_amount' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];

    public function redemptions()
    {
        return $this->hasMany(GiftCodeRedemption::class);
    }
}
