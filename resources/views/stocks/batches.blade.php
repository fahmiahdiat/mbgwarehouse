@extends('layouts.gudang')

@section('title', 'Batch — ' . $product->name)

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-1">
                    <i class="bi bi-layers text-primary"></i>
                    Daftar Batch
                </h5>
                <div class="text-muted">
                    <span class="badge bg-dark">{{ $product->code }}</span>
                    {{ $product->name }}
                    <span class="badge bg-info ms-1">{{ $batches->count() }} batch</span>
                </div>
            </div>
            <a href="{{ route('stocks.card', $product) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <hr>

        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Total Stok</small>
                <strong>{{ $product->stock }} {{ $product->unit?->symbol }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Batch Aktif</small>
                <strong>{{ $batches->where('qty_remaining', '>', 0)->count() }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Batch Expired</small>
                <strong class="text-danger">{{ $batches->where('expiry_status', 'expired')->count() }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Segera Expired</small>
                <strong class="text-warning">{{ $batches->where('expiry_status', 'segera')->count() }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="bi bi-list-ul text-primary"></i>
            Detail Batch
        </h6>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="180">Kode Batch</th>
                        <th width="110">Tgl Masuk</th>
                        <th width="110">Tgl Expired</th>
                        <th class="text-end" width="90">Qty Awal</th>
                        <th class="text-end" width="90">Qty Sisa</th>
                        <th class="text-end" width="100">Harga</th>
                        <th width="100">Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $i => $b)
                        <tr class="{{ $b->qty_remaining <= 0 ? 'opacity-50' : '' }}">
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><span class="badge bg-dark">{{ $b->batch_code }}</span></td>
                            <td>{{ $b->entry_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $b->expired_date ? $b->expired_date->format('d/m/Y') : '—' }}
                                @if($b->expired_date && $b->days_until_expired !== null && $b->days_until_expired >= 0)
                                    <br><small class="text-muted">({{ $b->days_until_expired }} hari lagi)</small>
                                @endif
                            </td>
                            <td class="text-end">{{ $b->qty_initial }}</td>
                            <td class="text-end fw-bold">{{ $b->qty_remaining }}</td>
                            <td class="text-end">
                                {{ $b->price ? 'Rp ' . number_format($b->price, 0, ',', '.') : '—' }}
                            </td>
                            <td>{!! $b->expiry_badge !!}</td>
                            <td class="small text-muted">{{ $b->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada batch untuk produk ini</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection