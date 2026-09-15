<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;
use App\Models\User;
use App\Policies\SanaeiServerPolicy;
use Mockery;
use Tests\TestCase;

final class SanaeiServerPolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_view_requires_dashboard_access_and_permission(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('canAccessDashboard')->once()->andReturn(true);
        $user->shouldReceive('hasPermission')->with('provider-servers.view')->once()->andReturn(false);
        $user->shouldReceive('hasRole')->with('super-admin')->once()->andReturn(false);

        $server = $this->server('sanaei');
        $this->assertFalse((new SanaeiServerPolicy)->view($user, $server));
    }

    public function test_create_is_allowed_with_independent_permission(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('canAccessDashboard')->once()->andReturn(true);
        $user->shouldReceive('hasPermission')->with('provider-servers.create')->once()->andReturn(true);

        $this->assertTrue((new SanaeiServerPolicy)->create($user));
    }

    public function test_delete_is_denied_for_non_sanaei_provider_account(): void
    {
        $user = Mockery::mock(User::class);
        $server = $this->server('marzban');

        $this->assertFalse((new SanaeiServerPolicy)->delete($user, $server));
        $user->shouldNotHaveReceived('canAccessDashboard');
    }

    private function server(string $driver): ServiceProviderAccount
    {
        $server = new ServiceProviderAccount(['name' => 'Provider server']);
        $server->setRelation('provider', new ServiceProvider(['driver' => $driver]));
        return $server;
    }
}
