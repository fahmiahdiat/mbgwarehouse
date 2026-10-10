@extends('layouts.gudang')

@section('title', 'Mutasi Barang')

@section('content')

{{-- FILTER --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ $to }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Jenis Transaksi</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">— Semua Jenis —</option>
                    <option value="in"              @selected(request('type') == 'in')>Barang Masuk</option>
                    <option value="out"             @selected(request('type') == 'out')>Barang Keluar</option>
                    <option value="return_supplier" @selected(request('type') == 'return_supplier')>Retur Supplier</option>
                    <option value="return_customer" @selected(request('type') == 'return_customer')>Retur Gudang</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel"></i> Terapkan
                </button>
                @if(request()->hasAny(['from','to','type']))
                    <a href="{{ route('stocks.mutation') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- RINGKASAN ANGKA --}}
<div class="row g-3 mb-3">
    <div class="col-md">
        <div class="card h-100">
            <div class="card-body text-center">
                <small class="text-muted d-block">
                    <i class="bi bi-box-arrow-in-down text-success"></i> Barang Masuk
                </small>
                <h4 class="mb-0 text-success">{{ number_format($in) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card h-100">
            <div class="card-body text-center">
                <small class="text-muted d-block">
                    <i class="bi bi-box-arrow-up text-danger"></i> Barang Keluar
                </small>
                <h4 class="mb-0 text-danger">{{ number_format($out) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card h-100">
            <div class="card-body text-center">
                <small class="text-muted d-block">
                    <i class="bi bi-arrow-return-left text-warning"></i> Retur Supplier
                </small>
                <h4 class="mb-0 text-warning">{{ number_format($retSup) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card h-100">
            <div class="card-body text-center">
                <small class="text-muted d-block">
                    <i class="bi bi-arrow-return-right text-info"></i> Retur Gudang
                </small>
                <h4 class="mb-0 text-info">{{ number_format($retCust) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card h-100">
            <div class="card-body text-center">
                <small class="text-muted d-block">
                    <i class="bi bi-sliders text-secondary"></i> Penyesuaian
                </small>
                <h4 class="mb-0 text-secondary">
                    {{ $adj > 0 ? '+' : '' }}{{ number_format($adj) }}
                </h4>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL PERGERAKAN --}}
<div class="card">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="bi bi-list-ul text-primary"></i>
            Detail Pergerakan Barang
            <span class="text-muted small">
                ({{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} —
                 {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }})
            </span>
        </h6>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="110">Tanggal</th>
                        <th width="180">Kode Transaksi</th>
                        <th width="140">Jenis</th>
                        <th width="110">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-end" width="90">Qty</th>
                        <th width="80">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $t)
                        @foreach($t->details as $d)
                            <tr>
                                <td>{{ $t->date->format('d/m/Y') }}</td>
                                <td><span class="badge bg-dark">{{ $t->code }}</span></td>
                                <td>
                                    @switch($t->type)
                                        @case('in')<span class="badge bg-success">Masuk</span>@break
                                        @case('out')<span class="badge bg-danger">Keluar</span>@break
                                        @case('return_supplier')<span class="badge bg-warning text-dark">Retur Supplier</span>@break
                                        @case('return_customer')<span class="badge bg-info">Retur Gudang</span>@break
                                    @endswitch
                                </td>
                                <td><span class="badge bg-secondary">{{ $d->product->code }}</span></td>
                                <td>{{ $d->product->name }}</td>
                                <td class="text-end fw-semibold">{{ $d->qty }}</td>
                                <td>{{ $d->product->unit?->symbol ?? '—' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Tidak ada pergerakan pada periode ini</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $details->links() }}
    </div>
</div>

@endsection