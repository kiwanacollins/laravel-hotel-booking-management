<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DiagnosticController extends Controller
{
    /**
     * Check authentication and role status
     */
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user() ? [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ] : null,
            'session_data' => session()->all(),
        ]);
    }

    /**
     * Check database connectivity and data
     */
    public function checkDatabase()
    {
        try {
            $users = User::all()->count();
            $rooms = Room::all()->count();
            $transactions = Transaction::all()->count();
            
            return response()->json([
                'database_connected' => true,
                'users_count' => $users,
                'rooms_count' => $rooms,
                'transactions_count' => $transactions,
                'all_users' => User::select('id', 'name', 'email', 'role')->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'database_connected' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check role based access
     */
    public function checkRoleAccess()
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'User not authenticated',
            ], 401);
        }

        $user = Auth::user();
        $requiredRoles = ['Super', 'Admin', 'Customer'];
        
        return response()->json([
            'user_role' => $user->role,
            'required_roles' => $requiredRoles,
            'role_in_enum' => in_array($user->role, $requiredRoles),
            'role_exact_match' => [
                'Super' => $user->role === 'Super',
                'Admin' => $user->role === 'Admin',
                'Customer' => $user->role === 'Customer',
            ],
        ]);
    }

    /**
     * Check dashboard accessibility
     */
    public function checkDashboard()
    {
        if (!Auth::check()) {
            return response()->json([
                'accessible' => false,
                'reason' => 'User not authenticated',
            ], 401);
        }

        $user = Auth::user();
        $roles = ['Super', 'Admin', 'Customer'];
        $hasAccess = in_array($user->role, $roles);

        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'has_access' => $hasAccess,
            'allowed_roles' => $roles,
            'access_granted' => $hasAccess ? 'YES' : 'NO',
        ]);
    }
}
