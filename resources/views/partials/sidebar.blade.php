@php
    $isActive = function ($patterns) {
        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) return true;
        }
        return false;
    };
@endphp

<aside class="sidebar">

    {{-- UTAMA --}}
    <div class="nav-section">Utama</div>
    <a href="{{ route('dashboard') }}" class="{{ $isActive('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    {{-- MASTER DATA --}}
    <div class="nav-section">Master Data</div>
    <a href="{{ route('products.index') }}" class="{{ $isActive('products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Data Barang
    </a>
    <a href="{{ route('categories.index') }}" class="{{ $isActive('categories.*') ? 'active' : '' }}">
        <i class="bi bi-tags"></i> Kategori
    </a>
    <a href="{{ route('units.index') }}" class="{{ $isActive('units.*') ? 'active' : '' }}">
        <i class="bi bi-rulers"></i> Satuan
    </a>
    <a href="{{ route('suppliers.index') }}" class="{{ $isActive('suppliers.*') ? 'active' : '' }}">
        <i class="bi bi-truck"></i> Supplier
    </a>
    <a href="{{ route('locations.index') }}" class="{{ $isActive('locations.*') ? 'active' : '' }}">
        <i class="bi bi-geo-alt"></i> Lokasi
    </a>

    {{-- TRANSAKSI --}}
    <div class="nav-section">Transaksi</div>
    <a href="{{ route('transactions.index', 'in') }}"
       class="{{ $isActive('transactions.index') && request()->route('type') === 'in' ? 'active' : '' }}">
        <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
    </a>
    <a href="{{ route('transactions.index', 'out') }}"
       class="{{ $isActive('transactions.index') && request()->route('type') === 'out' ? 'active' : '' }}">
        <i class="bi bi-box-arrow-up"></i> Barang Keluar
    </a>
    <a href="{{ route('transactions.index', 'return_supplier') }}"
       class="{{ $isActive('transactions.index') && request()->route('type') === 'return_supplier' ? 'active' : '' }}">
        <i class="bi bi-arrow-return-left"></i> Retur ke Supplier
    </a>
    <a href="{{ route('transactions.index', 'return_customer') }}"
       class="{{ $isActive('transactions.index') && request()->route('type') === 'return_customer' ? 'active' : '' }}">
        <i class="bi bi-arrow-return-right"></i> Retur ke Gudang
    </a>

    {{-- STOCK OPNAME --}}
    <div class="nav-section">Stock Opname</div>
    <a href="{{ route('opnames.index') }}" class="{{ $isActive('opnames.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-check"></i> Stock Opname
    </a>
    <a href="{{ route('adjustments.index') }}" class="{{ $isActive('adjustments.*') ? 'active' : '' }}">
        <i class="bi bi-sliders"></i> Penyesuaian Stok
    </a>

    {{-- MONITORING --}}
    <div class="nav-section">Monitoring</div>
    <a href="{{ route('stocks.index') }}" class="{{ $isActive('stocks.index') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i> Stok Barang
    </a>
    <a href="{{ route('stocks.mutation') }}" class="{{ $isActive('stocks.mutation') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i> Mutasi Barang
    </a>

    {{-- LAPORAN --}}
    <div class="nav-section">Laporan</div>
    <a href="{{ route('reports.stock') }}" class="{{ $isActive('reports.stock') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> Laporan Stok
    </a>
    <a href="{{ route('reports.in') }}" class="{{ $isActive('reports.in') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-arrow-down"></i> Laporan Masuk
    </a>
    <a href="{{ route('reports.out') }}" class="{{ $isActive('reports.out') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-arrow-up"></i> Laporan Keluar
    </a>
    <a href="{{ route('reports.opname') }}" class="{{ $isActive('reports.opname') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-check"></i> Laporan Opname
    </a>
    <a href="{{ route('reports.adjustment') }}" class="{{ $isActive('reports.adjustment') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-diff"></i> Laporan Penyesuaian
    </a>
    <a href="{{ route('reports.expired') }}" class="{{ $isActive('reports.expired') ? 'active' : '' }}">
        <i class="bi bi-calendar-x"></i> Laporan Expired
    </a>
    <a href="{{ route('reports.batch') }}" class="{{ $isActive('reports.batch') ? 'active' : '' }}">
        <i class="bi bi-layers"></i> Laporan Batch
    </a>

    {{-- PENGATURAN --}}
    <div class="nav-section">Pengaturan</div>
    <a href="{{ route('profile.edit') }}" class="{{ $isActive('profile.*') ? 'active' : '' }}">
        <i class="bi bi-person-circle"></i> Profil
    </a>
    <a href="{{ route('logs.index') }}" class="{{ $isActive('logs.*') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> Riwayat Aktivitas
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>

</aside>