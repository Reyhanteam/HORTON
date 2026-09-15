<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\SanaeiServers\SanaeiServerResource;
use App\Models\Role;
use App\Models\User;
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
}
