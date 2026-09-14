<?php

namespace App\Models;

class PlanPrice extends HortonModel
{
    protected $casts = ['amount' => 'integer', 'is_default' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
