<?php

namespace App\Models;

class Wallet extends HortonModel
{
    protected $casts = ['balance' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
