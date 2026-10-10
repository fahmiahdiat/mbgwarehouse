@extends('layouts.gudang')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan kondisi gudang hari ini')

@section('content')

{{-- ============================================================
     WELCOME BANNER
============================================================ --}}
<div class="welcome-banner">
    <div>
        <h5>Selamat Datang, {{ auth()->user()->name }}</h5>
        <p>Sistem Informasi Manajemen Gudang — SPPG Kuningan Jalaksana Maniskidul 3</p>
    </div>
    <div class="welcome-date">
        <i class="bi bi-calendar3"></i>
        {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>
</div>

{{-- ============================================================
     STAT CARDS
============================================================ --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card-new">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-body">
                <div class="stat-label">Total Barang</div>
                <div class="stat-value">{{ $totalBarang }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-new warning">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-body">
                <div class="stat-label">Stok Menipis</div>
                <div class="stat-value">{{ $stokMenipis }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-new danger">
            <div class="stat-icon"><i class="bi bi-x-octagon"></i></div>
            <div class="stat-body">
                <div class="stat-label">Stok Habis</div>
                <div class="stat-value">{{ $stokHabis }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     AKTIVITAS + PERINGATAN
============================================================ --}}
<div class="row g-3 mb-3">

    {{-- Aktivitas Hari Ini --}}
    <div class="col-md-5 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6><i class="bi bi-activity"></i> Aktivitas Hari Ini</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-3">
                                <i class="bi bi-box-arrow-in-down text-success me-2"></i>
                                Barang Masuk
                            </td>
                            <td class="text-end pe-3">
                                <span class="badge bg-success">{{ $masukHariIni }} transaksi</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3">
                                <i class="bi bi-box-arrow-up text-danger me-2"></i>
                                Barang Keluar
                            </td>
                            <td class="text-end pe-3">
                                <span class="badge bg-danger">{{ $keluarHariIni }} transaksi</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3">
                                <i class="bi bi-clipboard-check text-info me-2"></i>
                                Stock Opname
                            </td>
                            <td class="text-end pe-3">
                                <span class="badge bg-info">{{ $soHariIni }} transaksi</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Peringatan Stok --}}
    <div class="col-md-7 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="bi bi-bell"></i> Peringatan Stok</h6>
                @if($peringatan->count() > 0)
                    <a href="{{ route('stocks.index', ['filter' => 'menipis']) }}"
                       class="small text-decoration-none fw-semibold">
                        Lihat semua <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($peringatan->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check-circle" style="font-size: 42px; opacity: .25;"></i>
                        <p class="mb-0 mt-2">Semua stok dalam kondisi aman</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Barang</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">Min</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peringatan as $p)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold">{{ $p->name }}</div>
                                            <small class="text-muted">{{ $p->code }}</small>
                                        </td>
                                        <td class="text-end fw-bold">{{ $p->stock }}</td>
                                        <td class="text-end text-muted">{{ $p->min_stock }}</td>
                                        <td class="text-center">{!! $p->stock_status_badge !!}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('stocks.card', $p) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Kartu Stok">
                                                <i class="bi bi-journal-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     GRAFIK
============================================================ --}}
<div class="card mb-3">
    <div class="card-header">
        <h6>
            <i class="bi bi-graph-up"></i>
            Grafik Barang Masuk vs Barang Keluar
            <span class="text-muted fw-normal ms-1" style="font-size: 12px;">
                (7 hari terakhir)
            </span>
        </h6>
    </div>
    <div class="card-body">
        <div style="position: relative; height: 320px;">
            <canvas id="chartTransaksi"></canvas>
        </div>
    </div>
</div>

{{-- ============================================================
     AKSI CEPAT
============================================================ --}}
<div class="card">
    <div class="card-header">
        <h6><i class="bi bi-lightning-charge"></i> Aksi Cepat</h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3 col-6">
                <a href="{{ route('transactions.create', 'in') }}"
                   class="btn btn-outline-success w-100 py-3">
                    <i class="bi bi-box-arrow-in-down d-block mb-1" style="font-size: 20px;"></i>
                    <small class="fw-semibold">Barang Masuk</small>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('transactions.create', 'out') }}"
                   class="btn btn-outline-danger w-100 py-3">
                    <i class="bi bi-box-arrow-up d-block mb-1" style="font-size: 20px;"></i>
                    <small class="fw-semibold">Barang Keluar</small>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('opnames.create') }}"
                   class="btn btn-outline-primary w-100 py-3">
                    <i class="bi bi-clipboard-check d-block mb-1" style="font-size: 20px;"></i>
                    <small class="fw-semibold">Stock Opname</small>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('adjustments.create') }}"
                   class="btn btn-outline-secondary w-100 py-3">
                    <i class="bi bi-sliders d-block mb-1" style="font-size: 20px;"></i>
                    <small class="fw-semibold">Penyesuaian</small>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ============================================================
     CHART JS
============================================================ --}}
@push('scripts')
<script>
    const ctx = document.getElementById('chartTransaksi').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: @json($dataIn),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Barang Keluar',
                    data: @json($dataOut),
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#dc2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 18,
                        font: { size: 12, family: 'Plus Jakarta Sans', weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: '#1e3a8a',
                    padding: 12,
                    titleFont: { size: 13, family: 'Plus Jakarta Sans', weight: '700' },
                    bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    cornerRadius: 8,
                    displayColors: true,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: { size: 11, family: 'Plus Jakarta Sans' },
                        color: '#64748b'
                    },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    ticks: {
                        font: { size: 11, family: 'Plus Jakarta Sans' },
                        color: '#64748b'
                    },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush