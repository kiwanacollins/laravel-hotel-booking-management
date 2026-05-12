<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Debug logging
        \Log::info('Role Check', [
            'current_user_role' => $request->user()->role ?? 'No Role',
            'allowed_roles' => $roles,
            'user_id' => $request->user()->id ?? 'No User'
        ]);

        if ($request->user() && in_array($request->user()->role, $roles))
        {
            return $next($request);
        }

        // More detailed error logging
        \Log::warning('Dashboard Access Denied', [
            'user_role' => $request->user()->role ?? 'No Role',
            'required_roles' => $roles,
            'user_id' => $request->user()->id ?? 'No User'
        ]);

        return redirect('login')->with('failed', 'You are not authorized to access this page');
    }
}
