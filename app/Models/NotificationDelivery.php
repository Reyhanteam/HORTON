<?php

namespace App\Models;

class NotificationDelivery extends HortonModel
{
    protected $casts = ['attempts' => 'integer', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
