@extends('template.master')
@section('title', 'Annual Reports')

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

        .status-gold {
            background-color: #fff3cd;
            color: #664d03;
        }

        .top-customers-table tbody tr {
            border-bottom: 1px solid #e3e6f0;
        }

        .top-customers-table tbody tr:last-child {
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

        .year-selector {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .comparison-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .comparison-card {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
        }

        .comparison-card .label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .comparison-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .comparison-card .change {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .change.positive {
            color: #43e97b;
        }

        .change.negative {
            color: #f5576c;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-chart-line me-2"></i>Annual Reports</h1>
                <p class="text-muted">Year {{ $year }}</p>
            </div>
            <div class="year-selector">
                <form class="d-flex gap-2" method="GET" action="{{ route('report.annual') }}">
                    <input type="number" name="year" value="{{ $year }}" class="form-control" style="width: 120px;" min="2020">
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
                    <small class="text-muted">Year bookings</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card occupancy">
                    <div class="metric-label">Avg. Monthly Revenue</div>
                    <div class="metric-value">{{ Helper::convertToUGX($data['averageRevenuePerMonth']) }}</div>
                    <small class="text-muted">Per month average</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card">
                    <div class="metric-label">Top Customer Bookings</div>
                    <div class="metric-value">
                        {{ $data['topCustomers']->count() > 0 ? $data['topCustomers']->first()->bookings : 0 }}
                    </div>
                    <small class="text-muted">Highest bookings</small>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Monthly Revenue Trend</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Avg. Monthly Occupancy</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="occupancyChart"></canvas>
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
                        <div class="chart-title">Monthly Guest Activity</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="guestActivityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Year-over-Year Revenue Comparison</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="yoyComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers Performance -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="chart-title mb-0">Top Customers</div>
            </div>
            <div class="card-body">
                @if($data['topCustomers']->count() > 0)
                    <div class="table-responsive">
                        <table class="table top-customers-table mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Bookings</th>
                                    <th>Total Spent</th>
                                    <th>Avg. Stay</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['topCustomers'] as $index => $customer)
                                    @php
                                        $customerData = $customer->customer;
                                        $transactions = \App\Models\Transaction::where('customer_id', $customerData->id)
                                            ->whereYear('created_at', $year)->get();
                                        $totalSpent = $transactions->sum(function($t) { return $t->getTotalPrice(); });
                                        $avgStay = $transactions->count() > 0 
                                            ? $transactions->sum(function($t) { 
                                                return \Carbon\Carbon::parse($t->check_in)->diffInDays(\Carbon\Carbon::parse($t->check_out)); 
                                              }) / $transactions->count()
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="rank-badge">{{ $index + 1 }}</div>
                                        </td>
                                        <td><strong>{{ $customerData->name ?? 'N/A' }}</strong></td>
                                        <td>{{ $customerData->user->email ?? 'N/A' }}</td>
                                        <td>{{ $customer->bookings }}</td>
                                        <td>{{ Helper::convertToUGX($totalSpent) }}</td>
                                        <td>{{ number_format($avgStay, 1) }} days</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>No customer data available.
                    </div>
                @endif
            </div>
        </div>

        <!-- Annual Summary Stats -->
        <div class="comparison-stats">
            <div class="comparison-card">
                <div class="label">Annual Growth</div>
                <div class="value">+12.5%</div>
                <div class="change positive">
                    <i class="fas fa-arrow-up me-1"></i>vs Previous Year
                </div>
            </div>
            <div class="comparison-card">
                <div class="label">Best Month</div>
                <div class="value">
                    @php
                        $monthlyRevenue = $charts['monthlyRevenue'];
                        $maxRevenue = max($monthlyRevenue);
                        $bestMonth = array_search($maxRevenue, $monthlyRevenue) + 1;
                    @endphp
                    {{ \Carbon\Carbon::create(null, $bestMonth)->format('F') }}
                </div>
                <div>{{ Helper::convertToUGX($maxRevenue) }}</div>
            </div>
            <div class="comparison-card">
                <div class="label">Avg. Daily Occupancy</div>
                <div class="value">
                    @php
                        $avgOccupancy = array_sum($charts['monthlyOccupancy']) / count($charts['monthlyOccupancy']);
                    @endphp
                    {{ number_format($avgOccupancy, 1) }}%
                </div>
                <div>Across all months</div>
            </div>
            <div class="comparison-card">
                <div class="label">Avg. Daily Rate</div>
                <div class="value">
                    @php
                        $totalDays = 365;
                        $avgDailyRate = $data['totalRevenue'] / $totalDays;
                    @endphp
                    {{ Helper::convertToUGX($avgDailyRate) }}
                </div>
                <div>Revenue per day</div>
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

        // Monthly Revenue Chart
        const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($charts['monthNames']),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: @json($charts['monthlyRevenue']),
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

        // Occupancy Chart
        const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
        new Chart(occupancyCtx, {
            type: 'radar',
            data: {
                labels: @json($charts['monthNames']),
                datasets: [{
                    label: 'Occupancy %',
                    data: @json($charts['monthlyOccupancy']),
                    borderColor: chartColors.danger,
                    backgroundColor: 'rgba(245, 87, 108, 0.2)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: chartColors.danger
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
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Guest Activity Chart
        const guestCtx = document.getElementById('guestActivityChart').getContext('2d');
        new Chart(guestCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['monthNames']),
                datasets: [{
                    label: 'Avg. Daily Guests',
                    data: @json($charts['monthlyGuests']),
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

        // Year-over-Year Comparison
        const yoyCtx = document.getElementById('yoyComparisonChart').getContext('2d');
        new Chart(yoyCtx, {
            type: 'line',
            data: {
                labels: @json($charts['monthNames']),
                datasets: [
                    {
                        label: '{{ $year }}',
                        data: @json($charts['monthlyRevenue']),
                        borderColor: chartColors.success,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: chartColors.success
                    },
                    {
                        label: '{{ $year - 1 }}',
                        data: @json($charts['previousYearRevenue']),
                        borderColor: chartColors.warning,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: chartColors.warning
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
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
    </script>
@endsection
