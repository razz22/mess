<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // If impersonating, the original user must be super admin
        $adminUserId = session('impersonating_admin_id');
        if ($adminUserId) {
            $adminUser = \App\Models\User::find($adminUserId);
            if ($adminUser && $adminUser->is_super_admin) {
                return $next($request);
            }
        }

        if (!Auth::check() || !Auth::user()->is_super_admin) {
            abort(403, 'Super Admin access only.');
        }

        return $next($request);
    }
}
