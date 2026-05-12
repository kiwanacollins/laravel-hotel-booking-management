@extends('template.master')
@section('title', 'Daily Reports')

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

        .metric-card.guests .metric-value {
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

        .btn-group-sm .btn {
            font-size: 0.875rem;
            padding: 0.4rem 0.8rem;
        }

        .table-responsive {
            border-radius: 0.75rem;
        }

        .transaction-row {
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 0;
        }

        .transaction-row:last-child {
            border-bottom: none;
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

        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #2d3748;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-calendar-day me-2"></i>Daily Reports</h1>
                <p class="text-muted">{{ $selectedDate->format('l, F d, Y') }}</p>
            </div>
            <div>
                <form class="d-flex gap-2" method="GET" action="{{ route('report.daily') }}">
                    <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
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
                    <small class="text-muted">{{ $data['transactions']->count() }} payments</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card guests">
                    <div class="metric-label">Active Guests</div>
                    <div class="metric-value">{{ $data['activeGuests'] }}</div>
                    <small class="text-muted">Currently in hotel</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card occupancy">
                    <div class="metric-label">Occupancy Rate</div>
                    <div class="metric-value">{{ number_format($data['occupancyRate'], 1) }}%</div>
                    <small class="text-muted">Rooms occupied</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="metric-card">
                    <div class="metric-label">Avg. Room Price</div>
                    <div class="metric-value">{{ Helper::convertToUGX($data['averageRoomPrice']) }}</div>
                    <small class="text-muted">Per night</small>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="chart-title">Hourly Revenue</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
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
                        <div class="chart-title">Hourly Guest Activity</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="guestChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Transactions -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="chart-title mb-0">Today's Transactions</div>
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
                                        <td>{{ Helper::convertToUGX($transaction->getTotalPrice()) }}</td>
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
                        <i class="fas fa-info-circle me-2"></i>No transactions recorded for this date.
                    </div>
                @endif
            </div>
        </div>

        <!-- Current Guests -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="chart-title mb-0">Current Guests in Hotel</div>
                <span class="badge bg-primary">{{ $data['activeGuestsList']->count() }} guests</span>
            </div>
            <div class="card-body">
                @if($data['activeGuestsList']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Guest Name</th>
                                    <th>Room Number</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Days Left</th>
                                    <th>Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['activeGuestsList'] as $guest)
                                    <tr>
                                        <td>{{ $guest->customer->name ?? 'N/A' }}</td>
                                        <td><strong>{{ $guest->room->number ?? 'N/A' }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($guest->check_in)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($guest->check_out)->format('Y-m-d') }}</td>
                                        <td>
                                            @php $daysLeft = \Carbon\Carbon::parse($guest->check_out)->diffInDays(now()); @endphp
                                            <span class="badge {{ $daysLeft <= 0 ? 'bg-danger' : ($daysLeft == 1 ? 'bg-warning' : 'bg-success') }}">
                                                {{ $daysLeft <= 0 ? 'Last Day' : $daysLeft . ' days' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $guest->getTotalPrice() == $guest->getTotalPayment() ? 'status-paid' : 'status-pending' }}">
                                                {{ $guest->getTotalPrice() == $guest->getTotalPayment() ? 'Paid' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="fas fa-info-circle me-2"></i>No active guests in the hotel.
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

        // Hourly Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const hours = Array.from({length: 24}, (_, i) => i + ':00');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Revenue',
                    data: @json($charts['hourlyRevenue']),
                    borderColor: chartColors.success,
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: chartColors.success,
                    pointHoverRadius: 6
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

        // Hourly Guest Chart
        const guestCtx = document.getElementById('guestChart').getContext('2d');
        new Chart(guestCtx, {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Active Guests',
                    data: @json($charts['hourlyGuests']),
                    backgroundColor: chartColors.info,
                    borderColor: chartColors.primary,
                    borderWidth: 0
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
        const roomTypes = @json($charts['revenueByRoomType']->pluck('name')->values());
        const roomRevenue = @json($charts['revenueByRoomType']->pluck('total')->values());
        
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
    </script>
@endsection
