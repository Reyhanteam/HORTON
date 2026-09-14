<?php

namespace Tests\Unit\Auth;

use App\Models\BotSetting;
use App\Models\User;
use App\Policies\BotSettingPolicy;
use Mockery;
use Tests\TestCase;

final class BotSettingPolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_settings_are_denied_without_permission(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('canAccessDashboard')->andReturn(true);
        $user->shouldReceive('hasPermission')->with('settings.view')->andReturn(false);
        $user->shouldReceive('hasRole')->with('super-admin')->andReturn(false);

        self::assertFalse((new BotSettingPolicy())->viewAny($user));
    }

    public function test_super_admin_can_view_settings(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('canAccessDashboard')->andReturn(true);
        $user->shouldReceive('hasRole')->with('super-admin')->andReturn(true);

        self::assertTrue((new BotSettingPolicy())->viewAny($user));
        self::assertTrue((new BotSettingPolicy())->update($user, new BotSetting()));
    }
}
