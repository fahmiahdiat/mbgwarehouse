@extends('layouts.gudang')

@section('title', 'Laporan Stok')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-text text-primary"></i>
                Laporan Stok Barang
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.stock.export', request()->query()) }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>

        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Filter Status</label>
                <select name="filter" class="form-select form-select-sm">
                    <option value="">— Semua Status —</option>
                    <option value="aman"    @selected(request('filter') == 'aman')>🟢 Aman</option>
                    <option value="menipis" @selected(request('filter') == 'menipis')>🟡 Menipis</option>
                    <option value="habis"   @selected(request('filter') == 'habis')>🔴 Habis</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
                @if(request('filter'))
                    <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="110">Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Lokasi</th>
                        <th class="text-end" width="80">Stok</th>
                        <th class="text-end" width="80">Min</th>
                        <th width="110">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $p)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><span class="badge bg-dark">{{ $p->code }}</span></td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="text-muted small">{{ $p->category?->name ?? '—' }}</td>
                            <td>{{ $p->unit?->symbol ?? '—' }}</td>
                            <td class="text-muted small">{{ $p->location?->name ?? '—' }}</td>
                            <td class="text-end fw-bold">{{ $p->stock }}</td>
                            <td class="text-end text-muted">{{ $p->min_stock }}</td>
                            <td>{!! $p->stock_status_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-semibold">Total Barang</td>
                        <td class="text-end fw-bold">{{ $items->count() }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection