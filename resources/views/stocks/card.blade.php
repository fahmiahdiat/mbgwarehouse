@extends('layouts.gudang')

@section('title', 'Kartu Stok — ' . $product->name)

@section('content')

{{-- INFO BARANG --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-1">
                    <i class="bi bi-journal-text text-primary"></i>
                    Kartu Stok
                </h5>
                <div class="text-muted small">
                    <span class="badge bg-dark">{{ $product->code }}</span>
                    {{ $product->name }}
                </div>
            </div>
            <div class="d-flex gap-1">
    @if($product->has_batch)
        <a href="{{ route('stocks.batches', $product) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-layers"></i> Lihat Batch
        </a>
    @endif
    <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
        </div>

        <hr>

        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Kategori</small>
                <strong>{{ $product->category?->name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Lokasi</small>
                <strong>{{ $product->location?->name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Satuan</small>
                <strong>{{ $product->unit?->full_name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Stok Minimum</small>
                <strong>{{ $product->min_stock }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Stok Saat Ini</small>
                <h5 class="mb-0">
                    {{ $product->stock }}
                    <small class="text-muted">{{ $product->unit?->symbol }}</small>
                    {!! $product->stock_status_badge !!}
                </h5>
            </div>
        </div>
    </div>
</div>

{{-- FILTER TANGGAL --}}
<div class="card mb-3">
    <div class="card-body py-2">
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
                    <i class="bi bi-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['from','to']))
                    <a href="{{ route('stocks.card', $product) }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TABEL MUTASI --}}
<div class="card">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="bi bi-list-ul text-primary"></i>
            Riwayat Pergerakan Stok
            <span class="badge bg-secondary ms-1">{{ count($rows) }} baris</span>
        </h6>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="110">Tanggal</th>
                        <th width="180">Referensi</th>
                        <th width="130">Jenis</th>
                        <th class="text-end" width="100">Masuk</th>
                        <th class="text-end" width="100">Keluar</th>
                        <th class="text-end" width="110">Saldo</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $r)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y') }}</td>
                            <td><span class="badge bg-dark">{{ $r['reference'] }}</span></td>
                            <td>
                                @switch($r['type'])
                                    @case('in')
                                        <span class="badge bg-success">Masuk</span>
                                        @break
                                    @case('out')
                                        <span class="badge bg-danger">Keluar</span>
                                        @break
                                    @case('return_supplier')
                                        <span class="badge bg-warning text-dark">Retur Supplier</span>
                                        @break
                                    @case('return_customer')
                                        <span class="badge bg-info">Retur Gudang</span>
                                        @break
                                    @case('adjustment')
                                        <span class="badge bg-secondary">Penyesuaian</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-end text-success fw-semibold">
                                {{ $r['in'] > 0 ? '+' . $r['in'] : '' }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                {{ $r['out'] > 0 ? '-' . $r['out'] : '' }}
                            </td>
                            <td class="text-end fw-bold">{{ $r['saldo'] }}</td>
                            <td class="text-muted small">{{ $r['notes'] ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada pergerakan stok</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($rows) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-semibold">Saldo Akhir</td>
                            <td class="text-end fw-bold">{{ end($rows)['saldo'] ?? 0 }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection