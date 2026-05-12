<style>
    .dropend:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }

    #sidebar-wrapper {
        background: linear-gradient(135deg, #f0f8ff 0%, #e6f2ff 100%);
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link {
        color: #0099ff;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        margin: 0.25rem 0;
    }

    .nav-pills .nav-link:hover, 
    .nav-pills .nav-link.active {
        background-color: rgba(0, 153, 255, 0.1);
        color: #0099ff;
        transform: scale(1.05);
    }

    .sidebar-label {
        color: #007acc;
        font-weight: 600;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .nav-link:hover .sidebar-label {
        opacity: 1;
    }

    /* Subtle hover and active state animations */
    .nav-link {
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: all 0.5s ease;
    }

    .nav-link:hover::before {
        left: 100%;
    }

    .nav-link.active {
        box-shadow: 0 4px 6px rgba(0, 153, 255, 0.2);
    }
</style>
<div class="" id="sidebar-wrapper">
    <div class="d-flex flex-column"
        style="width: 4.5rem; border-top-right-radius:0.5rem; border-bottom-right-radius:0.5rem;">
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="mb-2 bg-white rounded cursor-pointer">
                <a href="{{ route('dashboard.index') }}"
                    class="nav-link py-3 border-bottom myBtn
                    {{ in_array(Route::currentRouteName(), ['dashboard.index', 'chart.dialyGuest']) ? 'active' : '' }}
                    "
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-label">Dashboard</span>
                </a>
            </li>
            @if (auth()->user()->role == 'Super' || auth()->user()->role == 'Admin')
                <li class="mb-2 bg-white rounded cursor-pointer">
                    <a href="{{ route('report.index') }}"
                        class="nav-link py-3 border-bottom border-right myBtn
                        {{ in_array(Route::currentRouteName(), ['report.index', 'report.daily', 'report.weekly', 'report.monthly', 'report.annual']) ? 'active' : '' }}
                        "
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                        <i class="fas fa-file-chart-line"></i>
                        <span class="sidebar-label">Reports</span>
                    </a>
                </li>
                <li class="mb-2 bg-white rounded cursor-pointer">
                    <a href="{{ route('transaction.index') }}"
                        class="nav-link py-3 border-bottom border-right myBtn
                        {{ in_array(Route::currentRouteName(), ['payment.index', 'transaction.index', 'transaction.reservation.createIdentity', 'transaction.reservation.pickFromCustomer', 'transaction.reservation.usersearch', 'transaction.reservation.storeCustomer', 'transaction.reservation.viewCountPerson', 'transaction.reservation.chooseRoom', 'transaction.reservation.confirmation', 'transaction.reservation.payDownPayment']) ? 'active' : '' }}
                        "
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Transactions">
                        <i class="fas fa-cash-register"></i>
                        <span class="sidebar-label">Transactions</span>
                    </a>
                </li>
                <li class="mb-2 bg-white rounded cursor-pointer">
                    <a class="nav-link py-3 border-bottom border-right myBtn  dropdown-toggle dropend
                    {{ in_array(Route::currentRouteName(), ['room.index', 'room.show', 'room.create', 'room.edit', 'type.index', 'type.create', 'type.edit', 'roomstatus.index', 'roomstatus.create', 'roomstatus.edit']) ? 'active' : '' }}
                        "
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-bs-toggle-tooltip="tooltip" data-bs-placement="right" title="Rooms">
                        <i class="fas fa-house-user"></i>
                        <span class="sidebar-label">Rooms</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('room.index') }}"><i class="fas fa-door-open me-2"></i>Room</a></li>
                        <li><a class="dropdown-item" href="{{ route('type.index') }}"><i class="fas fa-tag me-2"></i>Type</a></li>
                        <li><a class="dropdown-item" href="{{ route('roomstatus.index') }}"><i class="fas fa-check-circle me-2"></i>Status</a></li>
                        <li><a class="dropdown-item" href="{{ route('facility.index') }}"><i class="fas fa-star me-2"></i>Facility</a></li>
                    </ul>
                </li>
                <li class="mb-2 bg-white rounded cursor-pointer">
                    <a class="nav-link py-3 border-bottom border-right myBtn  dropdown-toggle
                        {{ in_array(Route::currentRouteName(), ['customer.index', 'customer.create', 'customer.edit', 'user.index', 'user.create', 'user.edit']) ? 'active' : '' }}
                    "
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-bs-toggle-tooltip="tooltip" data-bs-placement="right" title="People">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-label">People</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('customer.index') }}"><i class="fas fa-user-tie me-2"></i>Customer</a></li>
                        @if (auth()->user()->role == 'Super')
                            <li><a class="dropdown-item" href="{{ route('user.index') }}"><i class="fas fa-user-cog me-2"></i>User</a></li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
