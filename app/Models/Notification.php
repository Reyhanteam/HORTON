<?php

namespace App\Models;

class Notification extends HortonModel
{
    protected $casts = ['data' => 'array', 'read_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries()
    {
        return $this->hasMany(NotificationDelivery::class);
    }
}
