<?php

namespace App\Models;

class Campaign extends HortonModel
{
    protected $casts = ['configuration' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'usage_limit' => 'integer', 'is_active' => 'boolean'];
}
