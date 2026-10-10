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
    $m = $meta[$type] ?? ['title' => 'Transaksi', 'icon' => 'bi-list', 'color' => 'secondary'];
@endphp

@section('title', $m['title'])

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi {{ $m['icon'] }} text-{{ $m['color'] }}"></i>
                Daftar {{ $m['title'] }}
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('transactions.create', $type) }}"
               class="btn btn-{{ $m['color'] }} btn-sm">
                <i class="bi bi-plus-lg"></i> Transaksi Baru
            </a>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ request('to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-1">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                @if(request()->hasAny(['from','to']))
                    <a href="{{ route('transactions.index', $type) }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="180">Kode</th>
                        <th width="110">Tanggal</th>

                        @if(in_array($type, ['in', 'return_supplier']))
                            <th>Supplier</th>
                            <th width="150">No. Surat Jalan</th>
                        @else
                            <th>Penerima</th>
                            <th>Keperluan</th>
                        @endif

                        <th>Catatan</th>
                        <th width="100">User</th>
                        <th class="text-end" width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $t)
                        <tr>
                            <td><span class="badge bg-dark">{{ $t->code }}</span></td>
                            <td>{{ $t->date->format('d/m/Y') }}</td>

                            @if(in_array($type, ['in', 'return_supplier']))
                                <td>{{ $t->supplier?->name ?? '—' }}</td>
                                <td>{{ $t->delivery_note ?? '—' }}</td>
                            @else
                                <td>{{ $t->recipient ?? '—' }}</td>
                                <td class="text-muted small">{{ Str::limit($t->purpose, 40) ?? '—' }}</td>
                            @endif

                            <td class="text-muted small">{{ Str::limit($t->notes, 40) ?? '—' }}</td>
                            <td><small>{{ $t->user?->name ?? '—' }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('transactions.show', $t) }}"
                                   class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada transaksi</div>
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