@extends('layouts.gudang')

@php
    $meta = [
        'in' => [
            'title' => 'Barang Masuk',
            'icon'  => 'bi-box-arrow-in-down',
            'color' => 'success',
        ],
        'out' => [
            'title' => 'Barang Keluar',
            'icon'  => 'bi-box-arrow-up',
            'color' => 'danger',
        ],
        'return_supplier' => [
            'title' => 'Retur ke Supplier',
            'icon'  => 'bi-arrow-return-left',
            'color' => 'warning',
        ],
        'return_customer' => [
            'title' => 'Retur ke Gudang',
            'icon'  => 'bi-arrow-return-right',
            'color' => 'info',
        ],
    ];
    $m = $meta[$transaction->type] ?? ['title' => 'Transaksi', 'icon' => 'bi-list', 'color' => 'secondary'];
    $isIncoming = in_array($transaction->type, ['in', 'return_supplier']);
@endphp

@section('title', 'Detail ' . $m['title'])

@section('content')

{{-- HEADER INFO --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="mb-1">
                    <i class="bi {{ $m['icon'] }} text-{{ $m['color'] }}"></i>
                    {{ $m['title'] }}
                </h5>
                <div class="text-muted">
                    <span class="badge bg-dark">{{ $transaction->code }}</span>
                </div>
            </div>
            <a href="{{ route('transactions.index', $transaction->type) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Tanggal</small>
                <strong>{{ $transaction->date->format('d F Y') }}</strong>
            </div>

            @if($isIncoming)
                <div class="col-md-3">
                    <small class="text-muted d-block">Supplier</small>
                    <strong>{{ $transaction->supplier?->name ?? '—' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">No. Surat Jalan</small>
                    <strong>{{ $transaction->delivery_note ?? '—' }}</strong>
                </div>
            @else
                <div class="col-md-3">
                    <small class="text-muted d-block">Penerima / Bagian</small>
                    <strong>{{ $transaction->recipient ?? '—' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Keperluan</small>
                    <strong>{{ $transaction->purpose ?? '—' }}</strong>
                </div>
            @endif

            <div class="col-md-3">
                <small class="text-muted d-block">Dicatat oleh</small>
                <strong>{{ $transaction->user?->name ?? '—' }}</strong>
            </div>

            @if($transaction->notes)
                <div class="col-12">
                    <small class="text-muted d-block">Catatan</small>
                    <div>{{ $transaction->notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- DETAIL BARANG --}}
<div class="card">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="bi bi-list-ul text-primary"></i>
            Detail Barang ({{ $transaction->details->count() }} item)
        </h6>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="120">Kode</th>
                        <th>Nama Barang</th>
                        <th class="text-end" width="100">Qty</th>
                        <th width="80">Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->details as $i => $d)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <span class="badge bg-dark">{{ $d->product->code }}</span>
                            </td>
                            <td class="fw-semibold">{{ $d->product->name }}</td>
                            <td class="text-end">
                                <strong class="text-{{ $m['color'] }}">
                                    {{ $m['color'] === 'danger' || $transaction->type === 'return_supplier' ? '-' : '+' }}{{ $d->qty }}
                                </strong>
                            </td>
                            <td>{{ $d->product->unit?->symbol ?? '—' }}</td>
                            <td class="text-muted small">{{ $d->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Tidak ada detail barang
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Total Qty</td>
                        <td class="text-end fw-bold">{{ $transaction->total_qty }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection