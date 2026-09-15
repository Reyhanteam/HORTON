<?php

namespace App\Models;

class AuditLog extends HortonModel
{
    public $timestamps = false;

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }
}
