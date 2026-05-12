@extends('template.master')
@section('title', 'Reports')

@section('head')
    <style>
        .report-card {
            border-radius: 0.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .report-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .report-card.daily {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .report-card.weekly {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .report-card.monthly {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .report-card.annual {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .report-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .report-card h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .report-card p {
            margin: 0.5rem 0 0 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .report-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .stat-card h5 {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #6c757d;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-file-chart-line me-2"></i>Reports & Analytics</h1>
            <p>Generate comprehensive reports for your hotel management system</p>
        </div>

        <!-- Report Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('report.daily') }}" class="report-card daily">
                    <i class="fas fa-calendar-day"></i>
                    <h3>Daily Reports</h3>
                    <p>Today's overview and metrics</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('report.weekly') }}" class="report-card weekly">
                    <i class="fas fa-calendar-week"></i>
                    <h3>Weekly Reports</h3>
                    <p>This week's performance</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('report.monthly') }}" class="report-card monthly">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Monthly Reports</h3>
                    <p>Monthly analysis & trends</p>
                </a>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('report.annual') }}" class="report-card annual">
                    <i class="fas fa-chart-line"></i>
                    <h3>Annual Reports</h3>
                    <p>Yearly insights</p>
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="report-stats">
            <div class="stat-card">
                <h5>Total Revenue (This Month)</h5>
                <div class="stat-value">
                    {{ Helper::convertToUGX(\App\Models\Payment::whereMonth('created_at', now()->month)->sum('price')) }}
                </div>
            </div>
            <div class="stat-card">
                <h5>Total Bookings (This Month)</h5>
                <div class="stat-value">
                    {{ \App\Models\Transaction::whereMonth('created_at', now()->month)->count() }}
                </div>
            </div>
            <div class="stat-card">
                <h5>Current Occupancy</h5>
                <div class="stat-value">
                    @php
                        $activeGuests = \App\Models\Transaction::where([
                            ['check_in', '<=', now()->format('Y-m-d')],
                            ['check_out', '>=', now()->format('Y-m-d')]
                        ])->count();
                        $totalRooms = \App\Models\Room::count();
                        $occupancy = $totalRooms > 0 ? intval(($activeGuests / $totalRooms) * 100) : 0;
                    @endphp
                    {{ $occupancy }}%
                </div>
            </div>
            <div class="stat-card">
                <h5>Active Guests</h5>
                <div class="stat-value">{{ $activeGuests }}</div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4" style="border: 1px solid #e3e6f0; border-radius: 0.75rem;">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2 text-info"></i>About These Reports
                </h5>
                <p class="card-text mb-2">
                    Our reporting system provides comprehensive analytics for your hotel management needs:
                </p>
                <ul class="mb-0">
                    <li><strong>Daily Reports:</strong> Real-time updates on today's guests, revenue, and occupancy rates</li>
                    <li><strong>Weekly Reports:</strong> Trends and performance metrics for the current week</li>
                    <li><strong>Monthly Reports:</strong> Detailed analysis including top rooms and payment status</li>
                    <li><strong>Annual Reports:</strong> Year-over-year comparisons and long-term trends</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
