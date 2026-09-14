<?php

namespace App\Models;

class Permission extends HortonModel
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
}
