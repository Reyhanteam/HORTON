<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;
use Tests\TestCase;

final class UserAuthorizationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_inactive_dashboard_user_cannot_access_dashboard(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->status = 'inactive';
        $user->shouldReceive('roles')->never();

        self::assertFalse($user->canAccessDashboard());
    }

    public function test_active_dashboard_user_with_a_role_can_access_dashboard(): void
    {
        $roles = new class {
            public function exists(): bool
            {
                return true;
            }
        };

        $user = Mockery::mock(User::class)->makePartial();
        $user->status = 'active';
        $user->shouldReceive('roles')->once()->andReturn($roles);

        self::assertTrue($user->canAccessDashboard());
    }

    public function test_user_policy_requires_permission_for_user_management(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->status = 'active';
        $user->shouldReceive('canAccessDashboard')->andReturn(true);
        $user->shouldReceive('hasPermission')->with('users.view')->andReturn(false);
        $user->shouldReceive('hasRole')->with('super-admin')->andReturn(false);

        $policy = new UserPolicy();

        self::assertFalse($policy->viewAny($user));
    }

    public function test_super_admin_can_manage_users_without_individual_permission(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('canAccessDashboard')->andReturn(true);
        $user->shouldReceive('hasRole')->with('super-admin')->andReturn(true);

        $policy = new UserPolicy();

        self::assertTrue($policy->viewAny($user));
    }
}
