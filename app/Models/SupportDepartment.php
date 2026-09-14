<?php

namespace App\Models;

class SupportDepartment extends HortonModel
{
    protected $casts = ['sort_order' => 'integer', 'is_active' => 'boolean'];

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'department_id');
    }

    public function contents()
    {
        return $this->hasMany(SupportContent::class, 'department_id');
    }
}
