<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Icon --}}
    <link rel="icon" href="{{ asset('img/logo/sip.png') }}">
    {{-- style --}}
    @vite('resources/sass/app.scss')
    <title>@yield('title')</title>
    <style>
        :root {
            --sky-blue-50: #e6f2ff;
            --sky-blue-100: #b3e0ff;
            --sky-blue-200: #80cdff;
            --sky-blue-300: #4dbdff;
            --sky-blue-400: #1aadff;
            --sky-blue-500: #0099ff;
            --sky-blue-600: #007acc;
            --sky-blue-700: #005c99;
            --sky-blue-800: #003d66;
            --sky-blue-900: #001e33;
        }

        body {
            background-color: var(--sky-blue-50);
            font-family: 'Nunito', sans-serif;
            transition: background-color 0.3s ease;
        }

        #wrapper {
            background-color: transparent;
        }

        .card {
            background-color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: var(--sky-blue-500);
            border-color: var(--sky-blue-600);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--sky-blue-600);
            border-color: var(--sky-blue-700);
        }

        #sidebar-wrapper {
            background-color: white;
            border-right: 1px solid var(--sky-blue-100);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .nav-link {
            color: var(--sky-blue-700);
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--sky-blue-100) !important;
            color: var(--sky-blue-900);
        }

        footer {
            background-color: white !important;
            border-top: 1px solid var(--sky-blue-100) !important;
        }

        .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: white;
        }

        .table-hover tbody tr:hover {
            background-color: var(--sky-blue-50);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--sky-blue-50);
        }

        @keyframes subtle-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .pulse-animation {
            animation: subtle-pulse 2s infinite;
        }
    </style>
    @yield('head')
</head>

<body>
    <header>
        @include('template.include._navbar')
    </header>
    <main class="my-3">
        <!-- Modal -->
        <div class="modal fade" id="main-modal" tabindex="-1" aria-labelledby="main-modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button id="btn-modal-close" type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button id="btn-modal-save" type="button" class="btn btn-primary text-white">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex" id="wrapper">
            <!-- Sidebar -->
            @include('template.include._sidebar')
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    <footer class="footer mt-auto py-2 shadow-sm border-top mt-3">
        @include('template.include._footer')
    </footer>
    @vite('resources/js/app.js')
    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar hover effects
            const sidebarLinks = document.querySelectorAll('.nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.classList.add('pulse-animation');
                });
                link.addEventListener('mouseleave', function() {
                    this.classList.remove('pulse-animation');
                });
            });

            // Card hover animations
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                    this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.15)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
                });
            });
        });
    </script>
    @yield('footer')
</body>

</html>
