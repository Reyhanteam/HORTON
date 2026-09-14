<?php

namespace App\Models;

class Referral extends HortonModel
{
    protected $casts = ['qualified_at' => 'datetime', 'rewarded_at' => 'datetime'];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
