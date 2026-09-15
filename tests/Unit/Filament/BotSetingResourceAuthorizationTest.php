<?php

declare(strict_types=1);

namespace Tests\Unit\Filament;

use App\Filament\Resources\BotSetings\BotSetingResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

final class BotSetingResourceAuthorizationTest extends TestCase
{
    protected function tearDown(): void
    {
        Auth::logout();
        Mockery::close();

        parent::tearDown();
    }

    public function test_super_admin_can_register_bot_settings_navigation(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('isActive')->once()->andReturnTrue();
        $user->shouldReceive('hasRole')->with('super-admin')->once()->andReturnTrue();

        Auth::setUser($user);

        self::assertTrue(BotSetingResource::canViewAny());
        self::assertTrue(BotSetingResource::shouldRegisterNavigation());
    }

    public function test_active_non_super_admin_requires_settings_view_permission(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('isActive')->once()->andReturnTrue();
        $user->shouldReceive('hasRole')->with('super-admin')->once()->andReturnFalse();
        $user->shouldReceive('hasPermission')->with('settings.view')->once()->andReturnFalse();

        Auth::setUser($user);

        self::assertFalse(BotSetingResource::canViewAny());
        self::assertFalse(BotSetingResource::shouldRegisterNavigation());
    }

    public function test_inactive_super_admin_cannot_register_bot_settings_navigation(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('isActive')->once()->andReturnFalse();

        Auth::setUser($user);

        self::assertFalse(BotSetingResource::canViewAny());
        self::assertFalse(BotSetingResource::shouldRegisterNavigation());
    }
}
