<?php

namespace App\Models;

class Job extends HortonModel
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $casts = [
        'available_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
