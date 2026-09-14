<?php

namespace App\Models;

class BotChannel extends HortonModel
{
    protected $casts = ['is_required' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
}
