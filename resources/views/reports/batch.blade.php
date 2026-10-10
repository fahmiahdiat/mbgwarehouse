@extends('layouts.gudang')

@section('title', 'Laporan Batch')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-layers text-primary"></i>
                Laporan Batch Barang
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.batch.export', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>

        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Barang</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">— Semua Barang Batch —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                            {{ $p->code }} — {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">— Semua —</option>
                    <option value="available" @selected(request('status') == 'available')>Masih Ada</option>
                    <option value="empty"     @selected(request('status') == 'empty')>Kosong</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
                @if(request()->hasAny(['product_id','from','to','status']))
                    <a href="{{ route('reports.batch') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
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
                        <th width="180">Kode Batch</th>
                        <th width="110">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th width="100">Tgl Masuk</th>
                        <th width="100">Tgl Expired</th>
                        <th class="text-end" width="80">Qty Awal</th>
                        <th class="text-end" width="80">Qty Sisa</th>
                        <th class="text-end" width="100">Harga</th>
                        <th width="100">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $b)
                        <tr>
                            <td class="text-muted">{{ $items->firstItem() + $i }}</td>
                            <td><span class="badge bg-dark">{{ $b->batch_code }}</span></td>
                            <td><span class="badge bg-secondary">{{ $b->product->code }}</span></td>
                            <td>{{ $b->product->name }}</td>
                            <td>{{ $b->entry_date->format('d/m/Y') }}</td>
                            <td>{{ $b->expired_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end">{{ $b->qty_initial }}</td>
                            <td class="text-end fw-bold">{{ $b->qty_remaining }}</td>
                            <td class="text-end">
                                {{ $b->price ? 'Rp ' . number_format($b->price, 0, ',', '.') : '—' }}
                            </td>
                            <td>{!! $b->expiry_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada data batch
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