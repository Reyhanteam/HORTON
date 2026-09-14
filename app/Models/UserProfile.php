<?php

namespace App\Models;

class UserProfile extends HortonModel
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
