<?php

namespace App\Models;

class AuditLog extends HortonModel
{
    public $timestamps = false;

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
