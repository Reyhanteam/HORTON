<?php

namespace App\Models;

class FailedJob extends HortonModel
{
    public $timestamps = false;

    protected $casts = ['failed_at' => 'datetime'];
}
