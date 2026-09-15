<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_is_the_only_web_authentication_provider(): void
    {
        $this->assertSame('users', config('auth.guards.web.provider'));
        $this->assertSame(User::class, config('auth.providers.users.model'));
        $this->assertArrayNotHasKey('admin', config('auth.guards'));
        $this->assertArrayNotHasKey('admin_users', config('auth.providers'));
    }

    public function test_admin_users_table_does_not_exist_after_schema_migrations(): void
    {
        $this->assertFalse(Schema::hasTable('admin_users'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('telegram_accounts'));
    }

    public function test_a_web_user_with_a_role_can_access_the_filament_dashboard(): void
    {
        $user = User::factory()->create();
        $role = Role::query()->create(['name' => 'super-admin']);

        $user->roles()->attach($role);

        $this->assertInstanceOf(FilamentUser::class, $user);
        $this->assertTrue($user->canAccessDashboard());
        $this->assertTrue($user->canAccessPanel(app('filament')->getPanel('admin')));
    }
}
