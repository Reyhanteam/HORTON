<?php

namespace App\Models;

class BotSetting extends HortonModel
{
    protected $table = 'bot_settings';

    protected $casts = ['is_public' => 'boolean'];
}
