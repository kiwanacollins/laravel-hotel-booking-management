<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Customer;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the reports index page
     */
    public function index()
    {
        return view('report.index');
    }

    /**
     * Display daily reports
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $data = $this->getDailyData($selectedDate);
        $charts = $this->getDailyCharts($selectedDate);

        return view('report.daily', compact('data', 'charts', 'selectedDate'));
    }

    /**
     * Display weekly reports
     */
    public function weekly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $week = $request->input('week', now()->weekOfYear);

        $data = $this->getWeeklyData($year, $week);
        $charts = $this->getWeeklyCharts($year, $week);

        return view('report.weekly', compact('data', 'charts', 'year', 'week'));
    }

    /**
     * Display monthly reports
     */
    public function monthly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $data = $this->getMonthlyData($year, $month);
        $charts = $this->getMonthlyCharts($year, $month);

        return view('report.monthly', compact('data', 'charts', 'year', 'month'));
    }

    /**
     * Display annual reports
     */
    public function annual(Request $request)
    {
        $year = $request->input('year', now()->year);

        $data = $this->getAnnualData($year);
        $charts = $this->getAnnualCharts($year);

        return view('report.annual', compact('data', 'charts', 'year'));
    }

    /**
     * Get daily report data
     */
    private function getDailyData(Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $transactions = Transaction::whereBetween('created_at', [$startOfDay, $endOfDay])->get();
        $payments = Payment::whereBetween('created_at', [$startOfDay, $endOfDay])->get();
        $activeGuests = Transaction::where([
            ['check_in', '<=', $date->format('Y-m-d')],
            ['check_out', '>=', $date->format('Y-m-d')]
        ])->get();

        $totalRevenue = $payments->sum('price');
        $totalTransactions = $transactions->count();
        $activeGuestsCount = $activeGuests->count();
        $occupancyRate = $this->calculateOccupancyRate($activeGuests);
        $averageRoomPrice = $activeGuests->count() > 0 
            ? $activeGuests->sum(function($t) { return $t->room->price; }) / $activeGuests->count() 
            : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'activeGuests' => $activeGuestsCount,
            'occupancyRate' => $occupancyRate,
            'averageRoomPrice' => $averageRoomPrice,
            'transactions' => $transactions,
            'payments' => $payments,
            'activeGuestsList' => $activeGuests
        ];
    }

    /**
     * Get daily chart data
     */
    private function getDailyCharts(Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $hourlyRevenue = [];
        $hourlyGuests = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $hourStart = $startOfDay->copy()->addHours($hour);
            $hourEnd = $hourStart->copy()->addHour();

            $revenue = Payment::whereBetween('created_at', [$hourStart, $hourEnd])->sum('price');
            $guests = Transaction::where([
                ['check_in', '<=', $date->format('Y-m-d H:i:s')],
                ['check_out', '>=', $date->format('Y-m-d H:i:s')]
            ])->count();

            $hourlyRevenue[$hour] = $revenue;
            $hourlyGuests[$hour] = $guests;
        }

        // Revenue by room type
        $revenueByRoomType = Payment::whereBetween('payments.created_at', [$startOfDay, $endOfDay])
            ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            ->groupBy('types.name')
            ->selectRaw('types.name, SUM(payments.price) as total')
            ->get();

        return [
            'hourlyRevenue' => $hourlyRevenue,
            'hourlyGuests' => $hourlyGuests,
            'revenueByRoomType' => $revenueByRoomType
        ];
    }

    /**
     * Get weekly report data
     */
    private function getWeeklyData($year, $week)
    {
        $weekStart = Carbon::setISODate($year, $week, 1)->startOfDay();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $transactions = Transaction::whereBetween('created_at', [$weekStart, $weekEnd])->get();
        $payments = Payment::whereBetween('created_at', [$weekStart, $weekEnd])->get();

        $totalRevenue = $payments->sum('price');
        $totalTransactions = $transactions->count();
        $averageRevenuePerDay = $totalRevenue / 7;
        $occupancyRateAvg = 0;

        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $activeGuests = Transaction::where([
                ['check_in', '<=', $day->format('Y-m-d')],
                ['check_out', '>=', $day->format('Y-m-d')]
            ])->get();
            $occupancyRateAvg += $this->calculateOccupancyRate($activeGuests);
        }
        $occupancyRateAvg = $occupancyRateAvg / 7;

        return [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageRevenuePerDay' => $averageRevenuePerDay,
            'occupancyRateAvg' => $occupancyRateAvg,
            'transactions' => $transactions,
            'payments' => $payments,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd
        ];
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyCharts($year, $week)
    {
        $weekStart = Carbon::setISODate($year, $week, 1)->startOfDay();
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dailyRevenue = [];
        $dailyGuests = [];
        $roomTypeRevenue = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $dayEnd = $day->copy()->endOfDay();

            $revenue = Payment::whereBetween('created_at', [$day, $dayEnd])->sum('price');
            $guests = Transaction::where([
                ['check_in', '<=', $day->format('Y-m-d')],
                ['check_out', '>=', $day->format('Y-m-d')]
            ])->count();

            $dailyRevenue[] = $revenue;
            $dailyGuests[] = $guests;
        }

        // Revenue by room type for the week
        $roomTypeRevenue = Payment::whereBetween('payments.created_at', [$weekStart, $weekStart->copy()->endOfWeek()])
            ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            ->groupBy('types.name')
            ->selectRaw('types.name, SUM(payments.price) as total')
            ->get();

        return [
            'dayNames' => $dayNames,
            'dailyRevenue' => $dailyRevenue,
            'dailyGuests' => $dailyGuests,
            'roomTypeRevenue' => $roomTypeRevenue
        ];
    }

    /**
     * Get monthly report data
     */
    private function getMonthlyData($year, $month)
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $transactions = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])->get();
        $payments = Payment::whereBetween('created_at', [$monthStart, $monthEnd])->get();

        $totalRevenue = $payments->sum('price');
        $totalTransactions = $transactions->count();
        $averageRevenuePerDay = $totalRevenue / $monthStart->daysInMonth;
        
        $occupancyRateAvg = 0;
        $daysInMonth = $monthStart->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = Carbon::create($year, $month, $i);
            $activeGuests = Transaction::where([
                ['check_in', '<=', $day->format('Y-m-d')],
                ['check_out', '>=', $day->format('Y-m-d')]
            ])->get();
            $occupancyRateAvg += $this->calculateOccupancyRate($activeGuests);
        }
        $occupancyRateAvg = $occupancyRateAvg / $daysInMonth;

        $topRooms = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('room_id')
            ->selectRaw('room_id, COUNT(*) as bookings')
            ->with('room')
            ->get()
            ->sortByDesc('bookings')
            ->take(5);

        return [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageRevenuePerDay' => $averageRevenuePerDay,
            'occupancyRateAvg' => $occupancyRateAvg,
            'topRooms' => $topRooms,
            'transactions' => $transactions,
            'payments' => $payments
        ];
    }

    /**
     * Get monthly chart data
     */
    private function getMonthlyCharts($year, $month)
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $daysInMonth = $monthStart->daysInMonth;

        $dailyRevenue = [];
        $dailyOccupancy = [];
        $dayLabels = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = Carbon::create($year, $month, $i);
            $dayEnd = $day->copy()->endOfDay();

            $revenue = Payment::whereBetween('created_at', [$day, $dayEnd])->sum('price');
            $activeGuests = Transaction::where([
                ['check_in', '<=', $day->format('Y-m-d')],
                ['check_out', '>=', $day->format('Y-m-d')]
            ])->get();
            $occupancy = $this->calculateOccupancyRate($activeGuests);

            $dailyRevenue[] = $revenue;
            $dailyOccupancy[] = $occupancy;
            $dayLabels[] = $i;
        }

        // Revenue by room type
        $roomTypeRevenue = Payment::whereBetween('payments.created_at', [$monthStart, $monthEnd])
            ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
            ->join('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->join('types', 'rooms.type_id', '=', 'types.id')
            ->groupBy('types.name')
            ->selectRaw('types.name, SUM(payments.price) as total')
            ->get();

        // Payment status breakdown
        $paymentStatus = Payment::whereBetween('created_at', [$monthStart, $monthEnd])
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count, SUM(price) as total')
            ->get();

        return [
            'dayLabels' => $dayLabels,
            'dailyRevenue' => $dailyRevenue,
            'dailyOccupancy' => $dailyOccupancy,
            'roomTypeRevenue' => $roomTypeRevenue,
            'paymentStatus' => $paymentStatus
        ];
    }

    /**
     * Get annual report data
     */
    private function getAnnualData($year)
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = $yearStart->copy()->endOfYear();

        $transactions = Transaction::whereBetween('created_at', [$yearStart, $yearEnd])->get();
        $payments = Payment::whereBetween('created_at', [$yearStart, $yearEnd])->get();

        $totalRevenue = $payments->sum('price');
        $totalTransactions = $transactions->count();
        $averageRevenuePerMonth = $totalRevenue / 12;

        $topCustomers = Transaction::whereBetween('created_at', [$yearStart, $yearEnd])
            ->groupBy('customer_id')
            ->selectRaw('customer_id, COUNT(*) as bookings')
            ->with('customer')
            ->get()
            ->sortByDesc('bookings')
            ->take(5);

        return [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageRevenuePerMonth' => $averageRevenuePerMonth,
            'topCustomers' => $topCustomers,
            'transactions' => $transactions,
            'payments' => $payments
        ];
    }

    /**
     * Get annual chart data
     */
    private function getAnnualCharts($year)
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyRevenue = [];
        $monthlyGuests = [];
        $monthlyOccupancy = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($year, $month, 1)->startOfDay();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $revenue = Payment::whereBetween('created_at', [$monthStart, $monthEnd])->sum('price');
            
            $guests = 0;
            $occupancy = 0;
            $daysInMonth = $monthStart->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day = Carbon::create($year, $month, $i);
                $dailyGuests = Transaction::where([
                    ['check_in', '<=', $day->format('Y-m-d')],
                    ['check_out', '>=', $day->format('Y-m-d')]
                ])->get();
                $guests += $dailyGuests->count();
                $occupancy += $this->calculateOccupancyRate($dailyGuests);
            }

            $monthlyRevenue[] = $revenue;
            $monthlyGuests[] = intval($guests / $daysInMonth);
            $monthlyOccupancy[] = intval($occupancy / $daysInMonth);
        }

        // Revenue comparison with previous year
        $previousYearStart = Carbon::create($year - 1, 1, 1)->startOfDay();
        $previousYearEnd = $previousYearStart->copy()->endOfYear();
        $previousYearRevenue = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($year - 1, $month, 1)->startOfDay();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $revenue = Payment::whereBetween('created_at', [$monthStart, $monthEnd])->sum('price');
            $previousYearRevenue[] = $revenue;
        }

        return [
            'monthNames' => $monthNames,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyGuests' => $monthlyGuests,
            'monthlyOccupancy' => $monthlyOccupancy,
            'previousYearRevenue' => $previousYearRevenue
        ];
    }

    /**
     * Calculate occupancy rate
     */
    private function calculateOccupancyRate($activeGuests)
    {
        $totalRooms = Room::count();
        if ($totalRooms == 0) return 0;
        
        return ($activeGuests->count() / $totalRooms) * 100;
    }

    /**
     * Export report as PDF
     */
    public function exportPDF($type, Request $request)
    {
        // Implementation for PDF export
        // This would require a PDF library like TCPDF or Dompdf
    }

    /**
     * Export report as Excel
     */
    public function exportExcel($type, Request $request)
    {
        // Implementation for Excel export
        // This would require a library like PhpSpreadsheet
    }
}
