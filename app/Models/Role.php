<?php

namespace App\Models;

class Role extends HortonModel
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function adminUsers()
    {
        return $this->belongsToMany(AdminUser::class, 'role_user', 'role_id', 'admin_user_id');
    }
}
