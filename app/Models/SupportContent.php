<?php

namespace App\Models;

class SupportContent extends HortonModel
{
    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    public function department()
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }
}
