@extends('template.master')
@section('title', 'Dashboard')
@section('content')
    <div id="dashboard">
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <div class="card shadow-sm border" style="border-radius: 0.5rem; background-color: #e6f2ff;">
                            <div class="card-body">
                                <h5>Room Status Overview</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-success me-2">Available: {{ $availableRooms }}</span>
                                        <span class="badge bg-danger">Occupied: {{ $occupiedRooms }}</span>
                                    </div>
                                    <div class="text-muted">Total Rooms: {{ $totalRooms }}</div>
                                </div>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ ($availableRooms / $totalRooms) * 100 }}%" 
                                         aria-valuenow="{{ ($availableRooms / $totalRooms) * 100 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" 
                                         style="width: {{ ($occupiedRooms / $totalRooms) * 100 }}%" 
                                         aria-valuenow="{{ ($occupiedRooms / $totalRooms) * 100 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow-sm border" style="border-radius: 0.5rem; background-color: #e6f2ff;">
                            <div class="card-body text-center">
                                <h5>Guest Overview</h5>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="text-muted">Today's Guests:</span>
                                        <strong>{{ count($transactions) }}</strong>
                                    </div>
                                    <div>
                                        <span class="text-muted">Occupancy Rate:</span>
                                        <strong>{{ number_format(($occupiedRooms / $totalRooms) * 100, 1) }}%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border">
                            <div class="card-header">
                                <div class="row ">
                                    <div class="col-lg-12 d-flex justify-content-between">
                                        <h3>Today Guests</h3>
                                        <div>
                                            <a href="#" class="btn btn-tool btn-sm">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="#" class="btn btn-tool btn-sm">
                                                <i class="fas fa-bars"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Room</th>
                                            <th class="text-center">Stay</th>
                                            <th>Day Left</th>
                                            <th>Debt</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    <img src="{{ $transaction->customer->user->getAvatar() }}"
                                                        class="rounded-circle img-thumbnail" width="40" height="40"
                                                        alt="">
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('customer.show', ['customer' => $transaction->customer->id]) }}">
                                                        {{ $transaction->customer->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('room.show', ['room' => $transaction->room->id]) }}">
                                                        {{ $transaction->room->number }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ Helper::dateFormat($transaction->check_in) }} ~
                                                    {{ Helper::dateFormat($transaction->check_out) }}
                                                </td>
                                                <td>{{ Helper::getDateDifference(now(), $transaction->check_out) == 0 ? 'Last Day' : Helper::getDateDifference(now(), $transaction->check_out) . ' ' . Helper::plural('Day', Helper::getDateDifference(now(), $transaction->check_out)) }}
                                                </td>
                                                <td>
                                                    {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() <= 0 ? '-' : Helper::convertToUGX($transaction->getTotalPrice() - $transaction->getTotalPayment()) }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="justify-content-center badge {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() == 0 ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() == 0 ? 'Success' : 'Progress' }}
                                                    </span>
                                                    @if (Helper::getDateDifference(now(), $transaction->check_out) < 1)
                                                        <span class="justify-content-center badge bg-danger">
                                                            must finish payment
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    There's no data in this table
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{-- <div class="row justify-content-md-center mt-3">
                                    <div class="col-sm-10 d-flex mx-auto justify-content-md-center">
                                        <div class="pagination-block">
                                            {{ $transactions->onEachSide(1)->links('template.paginationlinks') }}
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">

                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Monthly Guests Chart</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <p class="d-flex flex-column">
                                        {{-- <span class="text-bold text-lg">Belum</span> --}}
                                        {{-- <span>Total Guests at {{ Helper::thisMonth() . '/' . Helper::thisYear() }}</span> --}}
                                    </p>
                                    {{-- <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> Belum
                                    </span>
                                    <span class="text-muted">Since last month</span>
                                </p> --}}
                                </div>
                                <div class="position-relative mb-4">
                                    <canvas this-year="{{ Helper::thisYear() }}" this-month="{{ Helper::thisMonth() }}"
                                        id="visitors-chart" height="400" width="100%" class="chartjs-render-monitor"
                                        style="display: block; width: 249px; height: 200px;"></canvas>
                                </div>
                                <div class="d-flex flex-row justify-content-between">
                                    <span class="mr-2">
                                        <i class="fas fa-square text-primary"></i> {{ Helper::thisMonth() }}
                                    </span>
                                    <span>
                                        <i class="fas fa-square text-gray"></i> Last month
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
{{-- @section('footer')
    <script src="{{ asset('style/js/chart.min.js') }}"></script>
    <script src="{{ asset('style/js/guestsChart.js') }}"></script>
    <script>
        function reloadJs(src) {
            src = $('script[src$="' + src + '"]').attr("src");
            $('script[src$="' + src + '"]').remove();
            $('<script/>').attr('src', src).appendTo('head');
        }

        Echo.channel('dashboard')
            .listen('.dashboard.event', (e) => {
                $("#dashboard").hide()
                $("#dashboard").load(window.location.href + " #dashboard");
                $("#dashboard").show(150)
                reloadJs('style/js/guestsChart.js');
                toastr.warning(e.message, "Hello, {{ auth()->user()->name }}");
            })
    </script>
@endsection --}}
<div class="row mb-3">
            <div class="col-lg-12">
                <div class="card shadow-sm border">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12 d-flex justify-content-between">
                                <h3>Room Status Details</h3>
                                <div>
                                    <a href="#room-status-details" class="btn btn-tool btn-sm" data-bs-toggle="collapse">
                                        <i class="fas fa-chevron-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body collapse" id="room-status-details">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Room Status</th>
                                        <th>Occupancy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roomStatusDistribution as $room)
                                        <tr>
                                            <td>{{ $room->number }}</td>
                                            <td>{{ $room->type_name }}</td>
                                            <td>
                                                <span class="badge {{ $room->status_name == 'Clean' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $room->status_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $room->occupancy_status == 'Occupied' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $room->occupancy_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
