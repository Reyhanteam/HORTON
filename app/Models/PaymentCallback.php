<?php

namespace App\Models;

class PaymentCallback extends HortonModel
{
    protected $casts = ['payload' => 'array', 'processed_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
