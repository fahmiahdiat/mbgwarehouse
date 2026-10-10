<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            --navy:          #1e3a8a;
            --navy-dark:     #172e6b;
            --navy-hover:    #1e40af;
            --navy-light:    #eff6ff;
            --gold:          #f59e0b;
            --gold-hover:    #d97706;
            --gold-light:    #fef3c7;

            --bg:            #f1f5f9;
            --surface:       #ffffff;
            --border:        #e2e8f0;
            --text:          #0f172a;
            --text-muted:    #64748b;

            --success:       #16a34a;
            --danger:        #dc2626;
            --warning:       #f59e0b;
            --info:          #0284c7;

            --topbar-h:      70px;
            --sidebar-w:     265px;
        }

        * { box-sizing: border-box; }

        html, body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13.5px;
            color: var(--text);
            background: var(--bg);
            margin: 0;
            min-height: 100vh;
        }

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
            padding: 0 24px;
            z-index: 1030;
            box-shadow: 0 1px 2px rgba(0,0,0,.03);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .topbar-logo {
            width: 46px;
            height: 46px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .topbar-title {
            min-width: 0;
            line-height: 1.25;
        }
        .topbar-title strong {
            display: block;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-title small {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
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
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all .15s;
        }
        .btn-user:hover {
            background: var(--bg);
            border-color: var(--navy);
            color: var(--navy);
        }
        .btn-user i { font-size: 18px; color: var(--navy); }

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
            z-index: 1020;
            padding-bottom: 20px;
        }
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }

        .sidebar .nav-section {
            padding: 18px 22px 6px;
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
            padding: 9px 22px;
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
            padding: 9px 22px;
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

        /* ================================================================
           MAIN CONTENT
        ================================================================ */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 22px 28px 48px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* Breadcrumb */
        .breadcrumb-bar {
            margin-bottom: 14px;
        }
        .breadcrumb-bar .breadcrumb {
            margin-bottom: 0;
            font-size: 12.5px;
        }
        .breadcrumb-bar .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color .15s;
        }
        .breadcrumb-bar .breadcrumb-item a:hover {
            color: var(--navy);
        }
        .breadcrumb-bar .breadcrumb-item.active {
            color: var(--navy);
            font-weight: 600;
        }
        .breadcrumb-bar .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #94a3b8;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 20px;
        }
        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            letter-spacing: -0.3px;
        }
        .page-header .subtitle {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ================================================================
           CARD GLOBAL STYLE
        ================================================================ */
        .card {
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            background: var(--surface);
        }
        .card-body { padding: 20px 22px; }

        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 22px;
            font-weight: 600;
            border-radius: 10px 10px 0 0;
        }
        .card-header h6 {
            margin: 0;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-header h6 i { color: var(--navy); }

        /* ================================================================
           STAT CARD BARU (Formal)
        ================================================================ */
        .stat-card-new {
            background: var(--surface);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid var(--border);
            border-left: 4px solid var(--navy);
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            transition: all .15s;
        }
        .stat-card-new:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15,23,42,.08);
        }
        .stat-card-new.warning { border-left-color: var(--warning); }
        .stat-card-new.danger  { border-left-color: var(--danger); }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
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

        .stat-body { min-width: 0; }
        .stat-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 3px;
        }
        .stat-value {
            font-size: 30px;
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
            padding: 22px 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(30,58,138,.18);
        }
        .welcome-banner h5 {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 4px 0;
            letter-spacing: -0.2px;
        }
        .welcome-banner p {
            font-size: 12.5px;
            color: rgba(255,255,255,.75);
            margin: 0;
        }
        .welcome-date {
            font-size: 12.5px;
            color: rgba(255,255,255,.9);
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.12);
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 500;
            flex-shrink: 0;
        }

        /* ================================================================
           TABLE STYLE
        ================================================================ */
        .table-sm td, .table-sm th { padding: .55rem .65rem; vertical-align: middle; }
        .table-hover tbody tr:hover { background: #f8fafc; }
        .table thead.table-light th {
            background: #f1f5f9;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--text-muted);
        }

        /* ================================================================
           BUTTON PRIMARY OVERRIDE
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
           BADGE
        ================================================================ */
        .badge { font-weight: 600; font-size: 11px; padding: .4em .65em; }

        /* ================================================================
           MOBILE
        ================================================================ */
        .sidebar-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--navy);
            padding: 6px 10px;
            border-radius: 6px;
            margin-right: 10px;
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s;
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 16px; }
            .sidebar-toggle { display: inline-flex; }
            .topbar-date { display: none; }
            .topbar-title strong { font-size: 13px; }
            .topbar-title small { font-size: 11px; }
            .welcome-banner { flex-direction: column; align-items: flex-start; gap: 12px; }
        }

        /* ================================================================
           PRINT
        ================================================================ */
        @media print {
            .topbar, .sidebar, .breadcrumb-bar,
            .no-print, .sidebar-toggle { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }

        /* ================================================================
           LINK
        ================================================================ */
        a { color: var(--navy); }
        a:hover { color: var(--navy-hover); }
    </style>

    @stack('styles')
</head>
<body>

    {{-- TOPBAR --}}
    @include('partials.topbar')

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- Breadcrumb --}}
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

        {{-- Page Header --}}
        <div class="page-header">
            <h1>@yield('title', 'Dashboard')</h1>
            @hasSection('subtitle')
                <div class="subtitle">@yield('subtitle')</div>
            @endif
        </div>

        {{-- Flash Messages --}}
        @include('partials.alert')

        {{-- Content --}}
        @yield('content')

    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Auto dismiss alert setelah 6 detik
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(el => {
                try { new bootstrap.Alert(el).close(); } catch(e) {}
            });
        }, 6000);

        // Toggle sidebar mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.sidebar-toggle');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('open');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>