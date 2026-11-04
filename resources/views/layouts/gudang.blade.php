<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIMG SPPG Kuningan Jalaksana Maniskidul 3</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ================================================================
           VARIABEL WARNA
        ================================================================ */
        :root {
            --navy:       #1e3a8a;
            --navy-dark:  #172e6b;
            --navy-hover: #1e40af;
            --navy-light: #eff6ff;
            --gold:       #f59e0b;
            --gold-hover: #d97706;
            --gold-light: #fef3c7;

            --bg:         #f1f5f9;
            --surface:    #ffffff;
            --border:     #e2e8f0;
            --text:       #0f172a;
            --text-muted: #64748b;

            --success: #16a34a;
            --danger:  #dc2626;
            --warning: #f59e0b;
            --info:    #0284c7;

            --topbar-h:  64px;
            --sidebar-w: 265px;
        }

        * { box-sizing: border-box; }

        html, body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 13.5px;
    color: var(--text);
    background: var(--bg);
    margin: 0;
    min-height: 100vh;
    /* JANGAN pakai overflow-x: hidden — itu bikin tabel tidak bisa scroll */
}
        body.sidebar-open { overflow: hidden; }

        /* ================================================================
           TOPBAR
        ================================================================ */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1040;
            box-shadow: 0 1px 2px rgba(0,0,0,.03);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .sidebar-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--navy);
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 18px;
            line-height: 1;
            flex-shrink: 0;
            cursor: pointer;
            transition: all .15s;
        }
        .sidebar-toggle:hover { background: var(--navy-light); }
        .sidebar-toggle:active { transform: scale(0.95); }

        .topbar-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .topbar-title { min-width: 0; line-height: 1.25; }
        .topbar-title strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-title small {
            display: block;
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .topbar-date {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-user {
            background: transparent;
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all .15s;
        }
        .btn-user:hover {
            background: var(--bg);
            border-color: var(--navy);
            color: var(--navy);
        }
        .btn-user i { font-size: 16px; color: var(--navy); }

        /* ================================================================
           SIDEBAR
        ================================================================ */
        .sidebar {
            position: fixed;
            top: var(--topbar-h);
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: var(--navy);
            color: #fff;
            overflow-y: auto;
            z-index: 1030;
            padding-bottom: 20px;
            transition: transform .28s cubic-bezier(.4,0,.2,1);
        }
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }

        .sidebar .nav-section {
            padding: 16px 20px 6px;
            font-size: 10.5px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.45);
            font-weight: 700;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.82);
            text-decoration: none;
            padding: 10px 20px;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }
        .sidebar a i {
            width: 18px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .sidebar a.active {
            background: var(--gold);
            color: var(--navy);
            font-weight: 700;
            border-left-color: #fff;
        }
        .sidebar a.active i { color: var(--navy); }

        .sidebar .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 20px;
            background: transparent;
            border: none;
            color: rgba(255,255,255,.82);
            text-align: left;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar .logout-btn:hover {
            background: var(--danger);
            color: #fff;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: var(--topbar-h) 0 0 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1029;
            opacity: 0;
            transition: opacity .28s;
        }
        .sidebar-overlay.show { opacity: 1; }

        /* ================================================================
           MAIN CONTENT
        ================================================================ */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 20px 24px 60px;
            min-height: calc(100vh - var(--topbar-h));
        }

        .breadcrumb-bar { margin-bottom: 12px; }
        .breadcrumb-bar .breadcrumb {
            margin-bottom: 0;
            font-size: 12px;
            flex-wrap: wrap;
        }
        .breadcrumb-bar .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
        }
        .breadcrumb-bar .breadcrumb-item a:hover { color: var(--navy); }
        .breadcrumb-bar .breadcrumb-item.active {
            color: var(--navy);
            font-weight: 600;
        }
        .breadcrumb-bar .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #94a3b8;
        }

        .page-header { margin-bottom: 16px; }
        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            letter-spacing: -0.3px;
        }
        .page-header .subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ================================================================
           CARD
        ================================================================ */
        .card {
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            background: var(--surface);
        }
        .card-body { padding: 18px 20px; }
        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 13px 20px;
            font-weight: 600;
            border-radius: 10px 10px 0 0;
        }
        .card-header h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-header h6 i { color: var(--navy); }

        /* ================================================================
           STAT CARD
        ================================================================ */
        .stat-card-new {
            background: var(--surface);
            border-radius: 10px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid var(--border);
            border-left: 4px solid var(--navy);
            transition: all .15s;
        }
        .stat-card-new.warning { border-left-color: var(--warning); }
        .stat-card-new.danger  { border-left-color: var(--danger); }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            background: var(--navy-light);
            color: var(--navy);
        }
        .stat-card-new.warning .stat-icon {
            background: var(--gold-light);
            color: var(--gold-hover);
        }
        .stat-card-new.danger .stat-icon {
            background: #fee2e2;
            color: var(--danger);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
            letter-spacing: -0.5px;
        }

        /* ================================================================
           WELCOME BANNER
        ================================================================ */
        .welcome-banner {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: #fff;
            border-radius: 12px;
            padding: 20px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            box-shadow: 0 4px 16px rgba(30,58,138,.18);
        }
        .welcome-banner h5 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        .welcome-banner p {
            font-size: 12px;
            color: rgba(255,255,255,.75);
            margin: 0;
        }
        .welcome-date {
            font-size: 12px;
            color: rgba(255,255,255,.9);
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.12);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            flex-shrink: 0;
            white-space: nowrap;
        }

        /* ================================================================
           TABLE — RESPONSIVE
        ================================================================ */
        .table-sm td, .table-sm th {
            padding: .5rem .55rem;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background: #f8fafc;
        }
        .table thead.table-light th {
            background: #f1f5f9;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Wrapper scroll horizontal */
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            max-width: 100%;
            display: block;
        }

        /* Scrollbar tipis (desktop) */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Tabel di dalam wrapper */
        .table-responsive .table {
            margin-bottom: 0;
            white-space: nowrap;
        }

        /* Card jangan motong tabel */
        .card { overflow: visible !important; }
        .card-body { overflow: visible; }

        /* ================================================================
           BUTTON
        ================================================================ */
        .btn-primary {
            background: var(--navy);
            border-color: var(--navy);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--navy-hover);
            border-color: var(--navy-hover);
        }
        .btn-outline-primary {
            color: var(--navy);
            border-color: var(--navy);
        }
        .btn-outline-primary:hover {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }

        /* ================================================================
           BADGE & LINK
        ================================================================ */
        .badge { font-weight: 600; font-size: 11px; padding: .4em .65em; }
        a { color: var(--navy); }
        a:hover { color: var(--navy-hover); }

        /* ================================================================
           RESPONSIVE — TABLET (<= 992px)
        ================================================================ */
        @media (max-width: 991.98px) {
            .sidebar-toggle { display: inline-flex; align-items: center; }

            .sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,.25);
            }
            .sidebar.open { transform: translateX(0); }

            .sidebar-overlay { display: block; }
            .sidebar-overlay.show { opacity: 1; }

            .main-content {
                margin-left: 0;
                padding: 16px 16px 60px;
            }

            .topbar { padding: 0 14px; }
            .topbar-date { display: none; }
            .topbar-logo { width: 38px; height: 38px; }
            .topbar-title strong { font-size: 13px; }
            .topbar-title small { font-size: 10.5px; }
        }

        /* ================================================================
           RESPONSIVE — HP (<= 576px)
        ================================================================ */
        @media (max-width: 575.98px) {
            html, body { font-size: 13px; }

            .topbar { padding: 0 10px; }
            .topbar-left { gap: 8px; }
            .topbar-logo { width: 34px; height: 34px; }
            .topbar-title strong {
                font-size: 12.5px;
                max-width: 180px;
            }
            .topbar-title small { display: none; }

            .btn-user { padding: 5px 8px; }
            .btn-user span { display: none; }
            .btn-user i { font-size: 18px; margin: 0; }
            .btn-user.dropdown-toggle::after { margin-left: 2px; }

            .main-content {
                padding: 12px 12px 60px;
            }

            .page-header h1 { font-size: 17px; }
            .page-header .subtitle { font-size: 11.5px; }

            .breadcrumb-bar .breadcrumb { font-size: 11px; }

            .card-body { padding: 14px 14px; }
            .card-header { padding: 11px 14px; }
            .card-header h6 { font-size: 13px; }

            /* Welcome banner stack */
            .welcome-banner {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 16px;
                gap: 10px;
            }
            .welcome-banner h5 { font-size: 14.5px; }
            .welcome-banner p { font-size: 11.5px; }
            .welcome-date { font-size: 11px; }

            /* Stat card compact */
            .stat-card-new { padding: 14px; gap: 12px; }
            .stat-icon { width: 42px; height: 42px; font-size: 20px; }
            .stat-value { font-size: 22px; }
            .stat-label { font-size: 10.5px; }

            /* Table HP — font lebih kecil & padding rapat */
            .table-responsive .table {
                font-size: 12.5px;
            }
            .table-responsive .table th,
            .table-responsive .table td {
                padding: .5rem .5rem;
            }
            .table-responsive .btn-sm {
                padding: .4rem .55rem;
                font-size: 13px;
            }

            /* Form control (cegah iOS auto-zoom) */
            .form-control, .form-select {
                font-size: 14px;
                padding: 8px 12px;
            }
            .form-control-sm, .form-select-sm {
                font-size: 13.5px;
                padding: 6px 10px;
            }

            /* Filter form jadi 1 kolom */
            .row.g-2 > [class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Pagination */
            .pagination { font-size: 12.5px; }
            .pagination .page-link { padding: 6px 10px; }

            /* Aksi cepat */
            .btn.py-3 { padding: 12px 8px !important; }
            .btn.py-3 small { font-size: 11.5px; }
            .btn.py-3 i { font-size: 18px !important; }
        }

        /* ================================================================
           iOS SAFE AREA
        ================================================================ */
        @supports (padding: max(0px)) {
            .topbar {
                padding-left: max(20px, env(safe-area-inset-left));
                padding-right: max(20px, env(safe-area-inset-right));
            }
            .main-content {
                padding-left: max(16px, env(safe-area-inset-left));
                padding-right: max(16px, env(safe-area-inset-right));
            }
        }

        /* ================================================================
           PRINT
        ================================================================ */
        @media print {
            .topbar, .sidebar, .sidebar-overlay, .breadcrumb-bar,
            .no-print, .sidebar-toggle, .btn, form { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
            .table-responsive { overflow: visible !important; }
        }

        /* ================================================================
           ANIMASI
        ================================================================ */
        .fade-in { animation: fadeIn .3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- TOPBAR --}}
    @include('partials.topbar')

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- OVERLAY mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- MAIN --}}
    <main class="main-content fade-in">

        @php
            $routeName = request()->route()?->getName() ?? 'dashboard';
            $segments  = explode('.', $routeName);
            $firstSeg  = $segments[0] ?? 'dashboard';
            $action    = $segments[1] ?? null;

            $pageMap = [
                'dashboard'    => 'Dashboard',
                'products'     => 'Data Barang',
                'categories'   => 'Kategori',
                'units'        => 'Satuan',
                'suppliers'    => 'Supplier',
                'locations'    => 'Lokasi',
                'transactions' => 'Transaksi',
                'opnames'      => 'Stock Opname',
                'adjustments'  => 'Penyesuaian Stok',
                'stocks'       => 'Monitoring Stok',
                'reports'      => 'Laporan',
                'logs'         => 'Riwayat Aktivitas',
                'profile'      => 'Profil',
            ];

            $actionMap = ['create' => 'Tambah', 'edit' => 'Edit', 'show' => 'Detail'];
            $currentLabel = $pageMap[$firstSeg] ?? 'Dashboard';
        @endphp

        <div class="breadcrumb-bar">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door"></i> Beranda
                        </a>
                    </li>
                    @if($firstSeg !== 'dashboard')
                        <li class="breadcrumb-item">{{ $currentLabel }}</li>
                    @endif
                    @if($action && isset($actionMap[$action]))
                        <li class="breadcrumb-item active">{{ $actionMap[$action] }}</li>
                    @endif
                </ol>
            </nav>
        </div>

        <div class="page-header">
            <h1>@yield('title', 'Dashboard')</h1>
            @hasSection('subtitle')
                <div class="subtitle">@yield('subtitle')</div>
            @endif
        </div>

        @include('partials.alert')

        @yield('content')

    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Auto dismiss alert
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(el => {
                try { new bootstrap.Alert(el).close(); } catch(e) {}
            });
        }, 6000);

        // ============================================================
        // SIDEBAR MOBILE
        // ============================================================
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle  = document.querySelector('.sidebar-toggle');

            if (!sidebar || !toggle || !overlay) return;

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('show');
                document.body.classList.add('sidebar-open');
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }

            function toggleSidebar() {
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleSidebar();
            });

            overlay.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) closeSidebar();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>