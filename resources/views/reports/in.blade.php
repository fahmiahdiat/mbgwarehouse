@extends('layouts.gudang')

@section('title', 'Laporan Barang Masuk')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-arrow-down text-success"></i>
                Laporan Barang Masuk
            </h6>
            <div class="d-flex gap-1">
                <a href="{{ route('reports.in.export', request()->query()) }}"
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
            <div class="col-md-3">
                <label class="form-label small mb-1">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">— Semua Supplier —</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
                @if(request()->hasAny(['from','to','supplier_id']))
                    <a href="{{ route('reports.in') }}" class="btn btn-outline-secondary btn-sm">
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
                        <th>Supplier</th>
                        <th width="130">No. SJ</th>
                        <th width="100">Barang</th>
                        <th class="text-end" width="80">Qty</th>
                        <th width="70">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse($items as $t)
                        @foreach($t->details as $d)
                            <tr>
                                <td class="text-muted">{{ $no++ }}</td>
                                <td><span class="badge bg-dark">{{ $t->code }}</span></td>
                                <td>{{ $t->date->format('d/m/Y') }}</td>
                                <td class="small">{{ $t->supplier?->name ?? '—' }}</td>
                                <td class="small">{{ $t->delivery_note ?? '—' }}</td>
                                <td>{{ $d->product->name }}</td>
                                <td class="text-end fw-semibold text-success">+{{ $d->qty }}</td>
                                <td>{{ $d->product->unit?->symbol ?? '—' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
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