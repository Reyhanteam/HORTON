<?php

namespace App\Models;

class TelegramAccount extends HortonModel
{
    protected $casts = ['last_seen_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
