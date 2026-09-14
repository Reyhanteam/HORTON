<?php

namespace Tests\Unit\Auth;

use App\Http\Middleware\EnsureAdminPermission;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class EnsureAdminPermissionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_missing_permission_is_denied(): void
    {
        $user = Mockery::mock();
        $user->shouldReceive('canAccessDashboard')->once()->andReturn(true);
        $user->shouldReceive('hasPermission')->with('users.view')->once()->andReturn(false);
        $user->shouldReceive('hasRole')->with('super-admin')->once()->andReturn(false);

        $request = Request::create('/admin/users');
        $request->setUserResolver(fn () => $user);

        $middleware = new EnsureAdminPermission();

        $this->expectException(Response::class);

        $middleware->handle($request, fn () => new Response('ok'), 'users.view');
    }
}
