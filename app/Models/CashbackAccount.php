<?php

namespace App\Models;

class CashbackAccount extends HortonModel
{
    protected $casts = ['balance' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashbackTransaction::class);
    }
}
