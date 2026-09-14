<?php

namespace App\Models;

class Session extends HortonModel
{
    protected $table = 'sessions';

    public $timestamps = false;

    protected $casts = [
        'last_activity' => 'integer',
    ];
}
