<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Current Active Transactions
            $transactions = Transaction::with('user', 'room', 'customer')
                ->where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()]])
                ->orderBy('check_out', 'ASC')
                ->orderBy('id', 'DESC')
                ->get();

            // Room Status Tracking
            $totalRooms = Room::count();
            $occupiedRooms = Transaction::where([
                ['check_in', '<=', Carbon::now()], 
                ['check_out', '>=', Carbon::now()]
            ])->distinct('room_id')->count('room_id');

            $availableRooms = $totalRooms - $occupiedRooms;

            // Room Status Distribution
            $roomStatusDistribution = Room::with(['type', 'status'])
                ->leftJoin('transactions', function($join) {
                    $join->on('rooms.id', '=', 'transactions.room_id')
                         ->where('transactions.check_in', '<=', Carbon::now())
                         ->where('transactions.check_out', '>=', Carbon::now());
                })
                ->select(
                    'rooms.id', 
                    'rooms.number', 
                    'types.name as type_name', 
                    'room_statuses.name as status_name',
                    DB::raw('CASE WHEN transactions.id IS NOT NULL THEN "Occupied" ELSE "Available" END as occupancy_status')
                )
                ->join('types', 'rooms.type_id', '=', 'types.id')
                ->join('room_statuses', 'rooms.room_status_id', '=', 'room_statuses.id')
                ->get();

            // Log successful dashboard data retrieval
            \Log::info('Dashboard data retrieved successfully', [
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'available_rooms' => $availableRooms
            ]);

            return view('dashboard.index', [
                'transactions' => $transactions,
                'totalRooms' => $totalRooms,
                'occupiedRooms' => $occupiedRooms,
                'availableRooms' => $availableRooms,
                'roomStatusDistribution' => $roomStatusDistribution
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Dashboard controller error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Optionally redirect with error message
            return redirect()->back()->with('error', 'An error occurred while loading the dashboard');
        }
    }
}
