@extends('layouts.gudang')

@section('title', 'Penyesuaian Stok')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-sliders text-primary"></i>
                Daftar Penyesuaian Stok
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('adjustments.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Penyesuaian
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
                    <a href="{{ route('adjustments.index') }}"
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
                        <th width="120">Tanggal</th>
                        <th width="120">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="text-end" width="120">Penyesuaian</th>
                        <th>Alasan</th>
                        <th width="150">Referensi</th>
                        <th width="120">User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $a)
                        <tr>
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
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada penyesuaian stok</div>
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