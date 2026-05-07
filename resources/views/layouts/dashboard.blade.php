<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ $appName }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2d6a4f;
            --secondary-color: #1b4332;
            --bg-light: #f8f9fa;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .nav-link {
            padding: 12px 24px;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background-color: #f0fdf4;
        }

        .nav-link.active {
            color: var(--primary-color);
            background-color: #f0fdf4;
            border-right: 3px solid var(--primary-color);
        }

        /* Main Content */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 24px;
        }

        .topbar {
            background-color: white;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            margin: -24px -24px 24px -24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* DataTables Custom */
        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            outline: none;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1rem;
        }
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .page-item .page-link {
            border-radius: 8px !important;
            border: none;
            color: #64748b;
            padding: 8px 14px;
            font-size: 0.875rem;
        }
        .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }
        .page-link:hover {
            background-color: #f1f5f9;
        }
        table.dataTable {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc;
            padding: 10px 12px !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        table.dataTable tbody td {
            padding: 8px 12px !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        /* Select2 Custom */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 10px;
            padding: 0.35rem 0;
            border: 1px solid #e2e8f0;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 1rem;
            color: #334155;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            z-index: 998;
            top: 0;
            left: 0;
        }

        @media (max-width: 992px) {
            #sidebar {
                left: calc(var(--sidebar-width) * -1);
            }

            #main-content {
                margin-left: 0;
            }

            #sidebar.active {
                left: 0;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
    @stack('css')
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <div class="brand-icon">T</div>
                <span>{{ $appName }}</span>
            </a>
        </div>
        <div class="nav flex-column py-4">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Master Data User
            </a>
            <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Master Data Siswa
            </a>
            <a href="{{ route('surah.index') }}" class="nav-link {{ request()->routeIs('surah.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark-fill"></i> Master Data Surah
            </a>
            <hr class="mx-4 text-muted">
            <a href="/logout" class="nav-link text-danger">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content -->
    <div id="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark p-0 me-3 d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <h5 class="mb-0 fw-bold">@yield('header')</h5>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none dropdown-toggle p-0" type="button"
                    data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=2d6a4f&color=fff"
                        class="rounded-circle me-2" width="32">
                    <span class="fw-medium">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-left me-2"></i>
                            Logout</a></li>
                </ul>
            </div>
        </div>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            $('#sidebar-toggle, #sidebar-overlay').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('#sidebar-overlay').toggleClass('active');
            });
        });

        function showMessage(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                html: text,
                showConfirmButton: false,
                timer: 5000
            });
        }

        function confirmation(title, text, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }
    </script>
    @stack('js')
</body>

</html>
