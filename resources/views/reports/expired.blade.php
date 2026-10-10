@extends('layouts.gudang')

@section('title', 'Laporan Barang Expired')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-calendar-x text-danger"></i>
                Laporan Barang Expired
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.expired.export', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>

        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Filter</label>
                <select name="filter" class="form-select form-select-sm">
                    <option value="segera"  @selected($filter == 'segera')>Segera Expired (30 hari)</option>
                    <option value="expired" @selected($filter == 'expired')>Sudah Expired</option>
                    <option value="semua"   @selected($filter == 'semua')>Semua Batch Ada Expired</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
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
                        <th width="110">Tgl Expired</th>
                        <th class="text-center" width="100">Hari Tersisa</th>
                        <th class="text-end" width="90">Sisa</th>
                        <th width="70">Satuan</th>
                        <th width="110">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $b)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><span class="badge bg-dark">{{ $b->batch_code }}</span></td>
                            <td><span class="badge bg-secondary">{{ $b->product->code }}</span></td>
                            <td>{{ $b->product->name }}</td>
                            <td>{{ $b->expired_date?->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($b->days_until_expired < 0)
                                    <span class="badge bg-danger">{{ abs($b->days_until_expired) }} hari lalu</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $b->days_until_expired }} hari</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">{{ $b->qty_remaining }}</td>
                            <td>{{ $b->product->unit?->symbol }}</td>
                            <td>{!! $b->expiry_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle" style="font-size: 40px; opacity: .3;"></i>
                                <div class="mt-2">Tidak ada batch yang perlu diperhatikan 👍</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection