<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\SanaeiServers\SanaeiServerResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SanaeiServerNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_see_sanaei_server_navigation(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->create(['name' => 'super-admin']);
        $user->roles()->attach($role);

        $this->actingAs($user);

        self::assertTrue(SanaeiServerResource::canViewAny());
        self::assertTrue(SanaeiServerResource::shouldRegisterNavigation());
    }

    public function test_user_with_provider_server_view_permission_can_see_navigation(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->create(['name' => 'provider-manager']);
        $permission = Permission::query()->create(['name' => 'provider-servers.view']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->actingAs($user);

        self::assertTrue(SanaeiServerResource::canViewAny());
        self::assertTrue(SanaeiServerResource::shouldRegisterNavigation());
    }

    public function test_user_without_provider_server_permission_cannot_see_navigation(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->create(['name' => 'support']);
        $user->roles()->attach($role);

        $this->actingAs($user);

        self::assertFalse(SanaeiServerResource::canViewAny());
        self::assertFalse(SanaeiServerResource::shouldRegisterNavigation());
    }

    public function test_inactive_super_admin_cannot_see_navigation(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);
        $role = Role::query()->create(['name' => 'super-admin']);
        $user->roles()->attach($role);

        $this->actingAs($user);

        self::assertFalse(SanaeiServerResource::canViewAny());
        self::assertFalse(SanaeiServerResource::shouldRegisterNavigation());
    }

    public function test_database_seeder_grants_provider_server_permissions_to_super_admin(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        $this->seed(DatabaseSeeder::class);

        $role = Role::query()->where('name', 'super-admin')->firstOrFail();
        $user->refresh();

        self::assertTrue($user->hasRole('super-admin'));
        self::assertSame(
            [
                'provider-servers.create',
                'provider-servers.delete',
                'provider-servers.health',
                'provider-servers.test',
                'provider-servers.update',
                'provider-servers.view',
            ],
            $role->permissions()->orderBy('name')->pluck('name')->all(),
        );
    }
}
