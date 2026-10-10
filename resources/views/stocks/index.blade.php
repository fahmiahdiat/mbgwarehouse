@extends('layouts.gudang')

@section('title', 'Stok Barang')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-bar-chart-line text-primary"></i>
                Stok Barang Saat Ini
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / kode barang..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">— Semua Kategori —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="filter" class="form-select form-select-sm">
                    <option value="">— Semua Status —</option>
                    <option value="aman"    @selected(request('filter') == 'aman')>🟢 Aman</option>
                    <option value="menipis" @selected(request('filter') == 'menipis')>🟡 Menipis</option>
                    <option value="habis"   @selected(request('filter') == 'habis')>🔴 Habis</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['search','category','filter']))
                    <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- RINGKASAN STATUS --}}
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <a href="{{ route('stocks.index', ['filter' => 'aman']) }}"
                   class="text-decoration-none">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <span class="text-success fw-semibold">
                                <i class="bi bi-check-circle-fill"></i> Aman
                            </span>
                            <span class="badge bg-success">
                                {{ \App\Models\Product::active()->safeStock()->count() }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('stocks.index', ['filter' => 'menipis']) }}"
                   class="text-decoration-none">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <span class="text-warning fw-semibold">
                                <i class="bi bi-exclamation-triangle-fill"></i> Menipis
                            </span>
                            <span class="badge bg-warning text-dark">
                                {{ \App\Models\Product::active()->lowStock()->count() }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('stocks.index', ['filter' => 'habis']) }}"
                   class="text-decoration-none">
                    <div class="card border-0 bg-danger bg-opacity-10">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <span class="text-danger fw-semibold">
                                <i class="bi bi-x-octagon-fill"></i> Habis
                            </span>
                            <span class="badge bg-danger">
                                {{ \App\Models\Product::active()->outOfStock()->count() }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="110">Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-end" width="80">Stok</th>
                        <th class="text-end" width="80">Min</th>
                        <th width="80">Satuan</th>
                        <th width="110">Status</th>
                        <th width="80">Lokasi</th>
                        <th class="text-end" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $p)
                        <tr>
                            <td><span class="badge bg-dark">{{ $p->code }}</span></td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="text-muted small">{{ $p->category?->name ?? '—' }}</td>
                            <td class="text-end fw-bold">{{ $p->stock }}</td>
                            <td class="text-end text-muted">{{ $p->min_stock }}</td>
                            <td>{{ $p->unit?->symbol ?? '—' }}</td>
                            <td>{!! $p->stock_status_badge !!}</td>
                            <td class="small text-muted">{{ $p->location?->name ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('stocks.card', $p) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Kartu Stok">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Tidak ada barang yang cocok dengan filter</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    </div>
</div>

@endsection