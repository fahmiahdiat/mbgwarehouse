@extends('layouts.gudang')

@section('title', 'Detail Stock Opname')

@section('content')

{{-- HEADER --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="mb-1">
                    <i class="bi bi-clipboard-check text-primary"></i>
                    Detail Stock Opname
                </h5>
                <div class="text-muted">
                    <span class="badge bg-dark">{{ $opname->code }}</span>
                    {!! $opname->status_badge !!}
                </div>
            </div>
            <a href="{{ route('opnames.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Tanggal</small>
                <strong>{{ $opname->date->format('d F Y') }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Dicatat oleh</small>
                <strong>{{ $opname->user?->name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Jumlah Item</small>
                <strong>{{ $opname->total_items }} barang</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Total Selisih</small>
                <strong class="{{ $opname->total_difference < 0 ? 'text-danger' : ($opname->total_difference > 0 ? 'text-success' : '') }}">
                    {{ $opname->total_difference > 0 ? '+' : '' }}{{ $opname->total_difference }}
                </strong>
            </div>
            @if($opname->notes)
                <div class="col-12">
                    <small class="text-muted d-block">Catatan</small>
                    <div>{{ $opname->notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- DETAIL TABLE --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-list-ul text-primary"></i>
                Detail Opname
            </h6>
            @if($opname->has_difference)
                <form action="{{ route('opnames.apply', $opname) }}"
                      method="POST"
                      onsubmit="return confirm('Buat penyesuaian otomatis untuk semua selisih? Stok akan diperbarui.')">
                    @csrf
                    <button class="btn btn-warning btn-sm">
                        <i class="bi bi-sliders"></i> Buat Penyesuaian Stok
                    </button>
                </form>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="120">Kode</th>
                        <th>Nama Barang</th>
                        <th class="text-end" width="120">Stok Sistem</th>
                        <th class="text-end" width="120">Stok Fisik</th>
                        <th class="text-end" width="100">Selisih</th>
                        <th width="100">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opname->details as $i => $d)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><span class="badge bg-dark">{{ $d->product->code }}</span></td>
                            <td>{{ $d->product->name }}</td>
                            <td class="text-end">{{ $d->system_stock }}</td>
                            <td class="text-end">{{ $d->physical_stock ?? '—' }}</td>
                            <td class="text-end fw-bold {{ $d->difference < 0 ? 'text-danger' : ($d->difference > 0 ? 'text-success' : '') }}">
                                {{ $d->difference > 0 ? '+' : '' }}{{ $d->difference }}
                            </td>
                            <td>{!! $d->difference_badge !!}</td>
                            <td class="text-muted small">{{ $d->notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection