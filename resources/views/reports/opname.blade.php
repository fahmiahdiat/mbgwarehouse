@extends('layouts.gudang')

@section('title', 'Laporan Stock Opname')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-check text-info"></i>
                Laporan Stock Opname
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.opname.export', request()->query()) }}"
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
                    <a href="{{ route('reports.opname') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@forelse($items as $so)
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge bg-dark">{{ $so->code }}</span>
                    <strong class="ms-2">{{ $so->date->format('d F Y') }}</strong>
                    {!! $so->status_badge !!}
                </div>
                <small class="text-muted">oleh {{ $so->user?->name ?? '—' }}</small>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th width="110">Kode</th>
                            <th>Barang</th>
                            <th class="text-end" width="110">Stok Sistem</th>
                            <th class="text-end" width="110">Stok Fisik</th>
                            <th class="text-end" width="100">Selisih</th>
                            <th width="90">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($so->details as $i => $d)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $d->product->code }}</span></td>
                                <td>{{ $d->product->name }}</td>
                                <td class="text-end">{{ $d->system_stock }}</td>
                                <td class="text-end">{{ $d->physical_stock ?? '—' }}</td>
                                <td class="text-end fw-bold {{ $d->difference < 0 ? 'text-danger' : ($d->difference > 0 ? 'text-success' : '') }}">
                                    {{ $d->difference > 0 ? '+' : '' }}{{ $d->difference }}
                                </td>
                                <td>{!! $d->difference_badge !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size: 40px; opacity: .3;"></i>
            <div class="mt-2">Belum ada data Stock Opname</div>
        </div>
    </div>
@endforelse

@endsection