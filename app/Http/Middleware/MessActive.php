<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $mess = $request->route('mess');

        if ($mess && $mess->status === 'inactive') {
            // Super admin can still act on inactive messes
            if (auth()->check() && auth()->user()->is_super_admin) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'This mess is currently inactive. Please contact the administrator.'], 403);
            }

            return redirect()->route('mess.dashboard', $mess->id)
                ->with('error', 'This mess is currently inactive. Some actions are disabled.');
        }

        return $next($request);
    }
}
