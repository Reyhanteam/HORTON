<?php

namespace App\Models;

class PaymentAttempt extends HortonModel
{
    protected $casts = ['request_payload' => 'array', 'response_payload' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
