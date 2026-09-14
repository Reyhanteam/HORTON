<?php

namespace App\Models;

class BotMessage extends HortonModel
{
    protected $casts = ['is_active' => 'boolean'];
}
