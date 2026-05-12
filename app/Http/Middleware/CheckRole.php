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
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect('login')->with('failed', 'Please log in first');
        }

        // Check if user has required role
        $userRole = $request->user()->role;
        
        // Log the role check attempt
        \Log::info('Role check', [
            'user_id' => $request->user()->id,
            'user_role' => $userRole,
            'required_roles' => $roles
        ]);

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Log unauthorized access attempt
        \Log::warning('Unauthorized access attempt', [
            'user_id' => $request->user()->id,
            'user_role' => $userRole,
            'required_roles' => $roles,
            'path' => $request->path()
        ]);

        return redirect()->back()->with('failed', 'You are not authorized to access this page');
    }
}
