<?php

namespace App\Models;

class Passkey extends HortonModel
{
    protected $casts = ['credential' => 'array', 'last_used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
