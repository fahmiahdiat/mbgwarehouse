@extends('layouts.gudang')

@section('title', 'Detail Barang')

@section('content')

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-box text-primary"></i>
                    Informasi Barang
                </h6>

                <table class="table table-sm">
                    <tr><td width="180" class="text-muted">Kode Barang</td><td><span class="badge bg-dark">{{ $product->code }}</span></td></tr>
                    <tr><td class="text-muted">Nama Barang</td><td class="fw-semibold">{{ $product->name }}</td></tr>
                    <tr><td class="text-muted">Kategori</td><td>{{ $product->category?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Satuan</td><td>{{ $product->unit?->full_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Supplier</td><td>{{ $product->supplier?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Lokasi</td><td>{{ $product->location?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Keterangan</td><td>{{ $product->description ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Status</td><td>
                        @if($product->status === 'active')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <small class="text-muted">Stok Saat Ini</small>
                <div style="font-size: 48px; font-weight: 700; line-height: 1;" class="my-2">
                    {{ $product->stock }}
                </div>
                <div class="mb-3">{{ $product->unit?->symbol }}</div>
                <div>{!! $product->stock_status_badge !!}</div>
                <div class="mt-3 text-muted small">
                    Stok minimum: <strong>{{ $product->min_stock }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <a href="{{ route('stocks.card', $product) }}" class="btn btn-info">
        <i class="bi bi-journal-text"></i> Lihat Kartu Stok
    </a>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@endsection