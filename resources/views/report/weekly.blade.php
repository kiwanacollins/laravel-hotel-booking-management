@extends('template.master')
@section('title', 'Weekly Reports')

@section('head')
    <style>
        .metric-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .metric-card .metric-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .metric-card .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .metric-card.revenue .metric-value {
            color: #43e97b;
        }

        .metric-card.transactions .metric-value {
            color: #4facfe;
        }

        .metric-card.occupancy .metric-value {
            color: #f5576c;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }

        .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
            font-weight: 600;
        }

        .table-responsive {
            border-radius: 0.75rem;
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #2d3748;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-paid {
            background-color: #d1f2eb;
            color: #0f5132;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #664d03;
        }

        .week-selector {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .week-info {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-calendar-week me-2"></i>Weekly Reports</h1>
                <p class="text-muted">Week {{ $week }}, {{ $year }}</p>
            </div>
            <div class="week-selector">
                <div class="week-info">
                    <strong>{{ $data['weekStart']->format('M d') }} - {{ $data['weekEnd']->format('M d, Y') }}</strong>
                </div>
                <form class="d-flex gap-2" method="GET" action="{{ route('report.weekly') }}">
                    <input type="number" name="year" value="{{ $year }}" class="form-control" style="width: 100px;" min="2020">
                    <input type="number" name="week" value="{{ $week }}" class="form-control" style="width: 100px;" min="1" max="53">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="metric-card revenue">
                    <div class="metric-label">Total Revenue</div>
                    <div class="metric-value">{{ Helper::convertToUGX($data['totalRevenue']) }}</div>
                    <small class="text-muted">{{ $data['payments']->count() }} payments</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card transactions">
                    <div class="metric-label">Total Bookings</div>
                    <div class="metric-value">{{ $data['totalTransactions'] }}</div>
                    <small class="text-muted">New reservations</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card occupancy">
                    <div class="metric-label">Avg. Daily Revenue</div>
                    <div class="metric-value">{{ Helper::convertToUGX($data['averageRevenuePerDay']) }}</div>
                    <small class="text-muted">Per day average</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card">
                    <div class="metric-label">Avg. Occupancy</div>
                    <div class="metric-value">{{ number_format($data['occupancyRateAvg'], 1) }}%</div>
                    <small class="text-muted">Weekly average</small>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Daily Revenue Trend</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="dailyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Revenue by Room Type</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="roomTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Daily Guest Activity</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="dailyGuestChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Comparison Chart</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Transactions Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="chart-title mb-0">Weekly Transactions</div>
                <span class="badge bg-primary">{{ $data['transactions']->count() }} transactions</span>
            </div>
            <div class="card-body">
                @if($data['transactions']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['transactions'] as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->id }}</td>
                                        <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->room->number ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->check_in)->format('Y-m-d') }}</td>
                                        <td>{{ number_format($transaction->getTotalPrice(), 0, ',', '.') }}</td>
                                        <td>
                                            <span class="status-badge {{ $transaction->status ? 'status-paid' : 'status-pending' }}">
                                                {{ $transaction->status == 1 ? 'Completed' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>No transactions recorded for this week.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        const chartColors = {
            primary: '#0099ff',     // Sky Blue
            success: '#4facfe',     // Lighter Sky Blue
            danger: '#ff6b6b',      // Soft Red
            warning: '#feca57',     // Soft Yellow
            info: '#1aadff'         // Vibrant Sky Blue
        };

        // Daily Revenue Chart
        const revenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($charts['dayNames']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($charts['dailyRevenue']),
                    borderColor: chartColors.success,
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: chartColors.success,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Daily Guest Chart
        const guestCtx = document.getElementById('dailyGuestChart').getContext('2d');
        new Chart(guestCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['dayNames']),
                datasets: [{
                    label: 'Active Guests',
                    data: @json($charts['dailyGuests']),
                    backgroundColor: chartColors.info,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'x',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Room Type Revenue Chart
        const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
        const roomTypes = @json($charts['roomTypeRevenue']->pluck('name')->values());
        const roomRevenue = @json($charts['roomTypeRevenue']->pluck('total')->values());
        
        const colors = ['#667eea', '#43e97b', '#f5576c', '#ffa502', '#4facfe'];
        
        new Chart(roomTypeCtx, {
            type: 'pie',
            data: {
                labels: roomTypes,
                datasets: [{
                    data: roomRevenue,
                    backgroundColor: colors.slice(0, roomTypes.length),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Comparison Chart (Revenue vs Guests)
        const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
        new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['dayNames']),
                datasets: [
                    {
                        label: 'Revenue',
                        data: @json($charts['dailyRevenue']),
                        backgroundColor: chartColors.success,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Guests',
                        data: @json($charts['dailyGuests']),
                        backgroundColor: chartColors.info,
                        borderRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Guest Count'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    </script>
@endsection
