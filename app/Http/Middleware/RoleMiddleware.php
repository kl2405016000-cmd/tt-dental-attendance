<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'You are not authorized to access this page.');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard')
                    ->with('error', 'Access denied. You do not have permission to access the Administrator Portal.');
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Access denied. Administrator accounts cannot access the Staff Portal.');
            }

            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
