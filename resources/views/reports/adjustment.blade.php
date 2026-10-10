@extends('layouts.gudang')

@section('title', 'Laporan Penyesuaian Stok')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-diff text-secondary"></i>
                Laporan Penyesuaian Stok
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.adjustment.export', request()->query()) }}"
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
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ request('to') }}">
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
                @if(request()->hasAny(['from','to']))
                    <a href="{{ route('reports.adjustment') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th width="160">Kode</th>
                        <th width="110">Tanggal</th>
                        <th width="110">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-end" width="120">Penyesuaian</th>
                        <th>Alasan</th>
                        <th width="140">Referensi</th>
                        <th width="120">User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $a)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><span class="badge bg-dark">{{ $a->code }}</span></td>
                            <td>{{ $a->date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $a->product->code }}</span></td>
                            <td>{{ $a->product->name }}</td>
                            <td class="text-end fw-bold {{ $a->adjustment < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $a->formatted_adjustment }}
                            </td>
                            <td class="small">{{ $a->reason }}</td>
                            <td class="small text-muted">{{ $a->reference ?? '—' }}</td>
                            <td><small>{{ $a->user?->name ?? '—' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection