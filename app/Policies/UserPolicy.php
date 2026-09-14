<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessDashboard() && ($user->hasPermission('users.view') || $user->hasRole('super-admin'));
    }

    public function view(User $user, User $model): bool
    {
        return $user->canAccessDashboard() && ($user->hasPermission('users.view') || $user->hasRole('super-admin'));
    }

    public function create(User $user): bool
    {
        return $user->canAccessDashboard() && ($user->hasPermission('users.create') || $user->hasRole('super-admin'));
    }

    public function update(User $user, User $model): bool
    {
        return $user->canAccessDashboard() && ($user->hasPermission('users.update') || $user->hasRole('super-admin'));
    }

    public function delete(User $user, User $model): bool
    {
        return $user->canAccessDashboard() && ($user->hasPermission('users.delete') || $user->hasRole('super-admin'));
    }
}
