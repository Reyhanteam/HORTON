<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        abort_unless($request->user()?->canAccessDashboard(), 403);
        abort_unless($request->user()?->hasPermission($permission) || $request->user()?->hasRole('super-admin'), 403);

        return $next($request);
    }
}
