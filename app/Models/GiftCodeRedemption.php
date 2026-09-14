<?php

namespace App\Models;

class GiftCodeRedemption extends HortonModel
{
    protected $casts = ['value' => 'integer'];

    public function giftCode()
    {
        return $this->belongsTo(GiftCode::class);
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
