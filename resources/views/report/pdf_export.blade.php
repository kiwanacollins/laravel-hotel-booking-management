<!DOCTYPE html>
<html>
<head>
    <title>Hotel Management System - {{ ucfirst($type) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #0099ff;
            text-align: center;
            border-bottom: 2px solid #0099ff;
            padding-bottom: 10px;
        }
        .report-summary {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0099ff;
            color: white;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>{{ ucfirst($type) }} Report</h1>
    
    <div class="report-summary">
        <h2>Key Metrics</h2>
        @switch($type)
            @case('daily')
                <p>Total Revenue: {{ Helper::convertToUGX($data['totalRevenue']) }}</p>
                <p>Active Guests: {{ $data['activeGuests'] }}</p>
                <p>Occupancy Rate: {{ number_format($data['occupancyRate'], 1) }}%</p>
                <p>Average Room Price: {{ Helper::convertToUGX($data['averageRoomPrice']) }}</p>
                @break
            @case('weekly')
                <p>Total Revenue: {{ Helper::convertToUGX($data['totalRevenue']) }}</p>
                <p>Total Bookings: {{ $data['totalTransactions'] }}</p>
                <p>Average Daily Revenue: {{ Helper::convertToUGX($data['averageRevenuePerDay']) }}</p>
                <p>Average Occupancy: {{ number_format($data['occupancyRateAvg'], 1) }}%</p>
                @break
            @case('monthly')
                <p>Total Revenue: {{ Helper::convertToUGX($data['totalRevenue']) }}</p>
                <p>Total Transactions: {{ $data['totalTransactions'] }}</p>
                <p>Average Daily Revenue: {{ Helper::convertToUGX($data['averageRevenuePerDay']) }}</p>
                <p>Average Occupancy: {{ number_format($data['occupancyRateAvg'], 1) }}%</p>
                @break
            @case('annual')
                <p>Total Annual Revenue: {{ Helper::convertToUGX($data['totalRevenue']) }}</p>
                <p>Total Bookings: {{ $data['totalTransactions'] }}</p>
                <p>Average Monthly Revenue: {{ Helper::convertToUGX($data['averageRevenuePerMonth']) }}</p>
                @break
        @endswitch
    </div>

    @if($type == 'daily')
        <h2>Today's Transactions</h2>
        <table>
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
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                        <td>{{ $transaction->room->number ?? 'N/A' }}</td>
                        <td>{{ Helper::convertToUGX($transaction->getTotalPrice()) }}</td>
                        <td>{{ $transaction->status == 1 ? 'Completed' : 'Pending' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Hotel Management System Report</p>
    </div>
</body>
</html>