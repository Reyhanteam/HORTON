<?php

namespace App\Models;

class Broadcast extends HortonModel
{
    protected $casts = ['media' => 'array', 'keyboard' => 'array', 'scheduled_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime'];

    public function recipients()
    {
        return $this->hasMany(BroadcastRecipient::class);
    }
}
