<?php

namespace App\Providers;

use App\Contracts\DiscountService;
use App\Contracts\PaymentGateway;
use App\Contracts\PricingService;
use App\Contracts\ServiceProviderContract;
use App\Events\UserRegistered;
use App\Listeners\InitializeUserAccount;
use App\Models\BotSetting;
use App\Models\User;
use App\Policies\BotSettingPolicy;
use App\Policies\UserPolicy;
use App\Services\CatalogPricingService;
use App\Services\DiscountCalculator;
use App\Services\Payments\FakePaymentGateway;
use App\Services\Providers\FakeServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PricingService::class, CatalogPricingService::class);
        $this->app->bind(DiscountService::class, DiscountCalculator::class);
        $this->app->bind(PaymentGateway::class, FakePaymentGateway::class);
        $this->app->bind(ServiceProviderContract::class, FakeServiceProvider::class);
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

        Event::listen(UserRegistered::class, InitializeUserAccount::class);
    }
}
