<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_users';
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['last_login_at' => 'datetime', 'password' => 'hashed'];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'admin_user_id', 'role_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'admin_user_id');
    }
}
