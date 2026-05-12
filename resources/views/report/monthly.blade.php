@extends('template.master')
@section('title', 'Monthly Reports')

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

        .top-rooms-table tbody tr {
            border-bottom: 1px solid #e3e6f0;
        }

        .top-rooms-table tbody tr:last-child {
            border-bottom: none;
        }

        .rank-badge {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-calendar-alt me-2"></i>Monthly Reports</h1>
                <p class="text-muted">{{ \Carbon\Carbon::create(null, $month)->format('F') }}, {{ $year }}</p>
            </div>
            <form class="d-flex gap-2" method="GET" action="{{ route('report.monthly') }}">
                <select name="month" class="form-select" style="width: auto;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                        </option>
                    @endfor
                </select>
                <input type="number" name="year" value="{{ $year }}" class="form-control" style="width: 100px;" min="2020">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </form>
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
                    <small class="text-muted">Monthly average</small>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Daily Revenue & Occupancy Trend</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueOccupancyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Payment Status</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="paymentStatusChart"></canvas>
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
                        <div class="chart-title">Revenue by Room Type</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="roomTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Daily Revenue Distribution</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Rooms Performance -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="chart-title mb-0">Top Performing Rooms</div>
            </div>
            <div class="card-body">
                @if($data['topRooms']->count() > 0)
                    <div class="table-responsive">
                        <table class="table top-rooms-table mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Room Number</th>
                                    <th>Room Type</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Occupancy Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['topRooms'] as $index => $room)
                                    @php
                                        $roomData = $room->room;
                                        $totalDays = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
                                        $occupancyRate = ($room->bookings / $totalDays) * 100;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="rank-badge">{{ $index + 1 }}</div>
                                        </td>
                                        <td><strong>{{ $roomData->number ?? 'N/A' }}</strong></td>
                                        <td>{{ $roomData->type->name ?? 'N/A' }}</td>
                                        <td>{{ $room->bookings }}</td>
                                        <td>{{ Helper::convertToUGX($room->bookings * $roomData->price) }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ min($occupancyRate, 100) }}%;" aria-valuenow="{{ $occupancyRate }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($occupancyRate, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>No room booking data available.
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Transactions -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="chart-title mb-0">Monthly Transactions</div>
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
                                    <th>Check-out</th>
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
                                        <td>{{ \Carbon\Carbon::parse($transaction->check_out)->format('Y-m-d') }}</td>
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
                        <i class="fas fa-info-circle me-2"></i>No transactions recorded for this month.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        const chartColors = {
            primary: '#667eea',
            success: '#43e97b',
            danger: '#f5576c',
            warning: '#ffa502',
            info: '#4facfe'
        };

        // Revenue & Occupancy Chart
        const revenueCtx = document.getElementById('revenueOccupancyChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['dayLabels']),
                datasets: [
                    {
                        label: 'Revenue',
                        data: @json($charts['dailyRevenue']),
                        backgroundColor: chartColors.success,
                        borderRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Occupancy %',
                        data: @json($charts['dailyOccupancy']),
                        type: 'line',
                        borderColor: chartColors.danger,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: chartColors.danger,
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
                            text: 'Occupancy %'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
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
            type: 'doughnut',
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

        // Payment Status Chart
        const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        const paymentStatus = @json($charts['paymentStatus']);
        const statusLabels = paymentStatus.map(item => item.status);
        const statusCounts = paymentStatus.map(item => item.count);
        
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: [chartColors.success, chartColors.warning],
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

        // Revenue Distribution Chart
        const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'line',
            data: {
                labels: @json($charts['dayLabels']),
                datasets: [{
                    label: 'Daily Revenue',
                    data: @json($charts['dailyRevenue']),
                    borderColor: chartColors.primary,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: chartColors.primary
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
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
