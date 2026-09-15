<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\DiscountService;
use App\Contracts\PaymentGateway;
use App\Contracts\PricingService;
use App\Contracts\ProviderSelectorContract;
use App\Contracts\ServiceProviderContract;
use App\Events\OrderPaid;
use App\Events\UserRegistered;
use App\Listeners\DispatchProvisionPaidOrder;
use App\Listeners\InitializeUserAccount;
use App\Models\BotSetting;
use App\Models\User;
use App\Policies\BotSettingPolicy;
use App\Policies\UserPolicy;
use App\Services\CatalogPricingService;
use App\Services\DiscountCalculator;
use App\Services\Payments\FakePaymentGateway;
use App\Services\Providers\FakeServiceProvider;
use App\Services\Providers\ProviderSelector;
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
        $this->app->bind(ProviderSelectorContract::class, ProviderSelector::class);
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(BotSetting::class, BotSettingPolicy::class);
        Gate::before(function (User $user): ?bool { return $user->hasRole('super-admin') ? true : null; });
        Gate::define('admin.access', fn (User $user): bool => $user->canAccessDashboard());
        Gate::define('admin.permission', fn (User $user, string $permission): bool => $user->isActive() && $user->hasPermission($permission));
        Event::listen(UserRegistered::class, InitializeUserAccount::class);
        Event::listen(OrderPaid::class, DispatchProvisionPaidOrder::class);
    }
}
