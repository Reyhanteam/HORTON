<?php

namespace App\Providers;

use App\Models\BotSetting;
use App\Models\User;
use App\Policies\BotSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(BotSetting::class, BotSettingPolicy::class);

        Gate::before(function (User $user): ?bool {
            if ($user->hasRole('super-admin')) {
                return true;
            }

            return null;
        });

        Gate::define('admin.access', fn (User $user): bool => $user->canAccessDashboard());

        Gate::define('admin.permission', function (User $user, string $permission): bool {
            return $user->isActive() && $user->hasPermission($permission);
        });
    }
}
