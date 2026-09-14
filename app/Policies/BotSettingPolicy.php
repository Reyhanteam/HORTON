<?php

namespace App\Policies;

use App\Models\BotSetting;
use App\Models\User;

class BotSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'settings.view');
    }

    public function view(User $user, BotSetting $setting): bool
    {
        return $this->allows($user, 'settings.view');
    }

    public function update(User $user, BotSetting $setting): bool
    {
        return $this->allows($user, 'settings.update');
    }

    private function allows(User $user, string $permission): bool
    {
        return $user->canAccessDashboard()
            && ($user->hasPermission($permission) || $user->hasRole('super-admin'));
    }
}
