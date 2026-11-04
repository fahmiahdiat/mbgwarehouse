@extends('layouts.gudang')

@section('title', 'Data Barang')
@section('subtitle', 'Master data seluruh barang di gudang')

@section('content')

<div class="card">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-box-seam text-primary"></i>
                Daftar Barang
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Barang
            </a>
        </div>

        {{-- FILTER --}}
        <div class="bg-light rounded p-3 mb-3">
            <form method="GET">
                <div class="row g-2">

                    {{-- Baris 1 --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted mb-1">Pencarian</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Cari nama / kode barang..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted mb-1">Kategori</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">— Semua Kategori —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>
                                    {{ $c->name }}@if(! $c->is_monitored) (tidak dipantau)@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted mb-1">Lokasi</label>
                        <select name="location" class="form-select form-select-sm">
                            <option value="">— Semua Lokasi —</option>
                            @foreach($locations as $l)
                                <option value="{{ $l->id }}" @selected(request('location') == $l->id)>
                                    {{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Baris 2 --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted mb-1">Monitoring</label>
                        <select name="monitoring" class="form-select form-select-sm">
                            <option value="">— Semua Monitoring —</option>
                            <option value="monitored"   @selected(request('monitoring') == 'monitored')>Dipantau</option>
                            <option value="unmonitored" @selected(request('monitoring') == 'unmonitored')>Tidak Dipantau</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted mb-1">Status Barang</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">— Semua Status —</option>
                            <option value="active"   @selected(request('status') == 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') == 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                        @if(request()->hasAny(['search','category','location','status','monitoring']))
                            <a href="{{ route('products.index') }}"
                               class="btn btn-outline-secondary btn-sm"
                               title="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="100">Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th width="70">Satuan</th>
                        <th>Lokasi</th>
                        <th class="text-end" width="70">Stok</th>
                        <th class="text-end" width="70">Min</th>
                        <th width="110">Status</th>
                        <th class="text-end" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $p)
                        <tr class="{{ $p->status === 'inactive' ? 'opacity-50' : '' }}">
                            <td><span class="badge bg-dark">{{ $p->code }}</span></td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $p->name }}
                                    @if($p->has_batch)
                                        <span class="badge bg-info ms-1"
                                              title="Wajib tracking batch & expired">
                                            <i class="bi bi-calendar-check"></i> Batch
                                        </span>
                                    @endif
                                </div>
                                @if($p->description)
                                    <small class="text-muted">{{ Str::limit($p->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $p->category?->name ?? '—' }}
                                @if($p->category && ! $p->category->is_monitored)
                                    <span class="badge bg-secondary ms-1"
                                          title="Kategori ini tidak dipantau">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                @endif
                            </td>
                            <td>{{ $p->unit?->symbol ?? '—' }}</td>
                            <td>{{ $p->location?->name ?? '—' }}</td>
                            <td class="text-end fw-bold">{{ $p->stock }}</td>
                            <td class="text-end text-muted">{{ $p->min_stock }}</td>
                            <td>{!! $p->stock_status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('stocks.card', $p) }}"
                                   class="btn btn-outline-info btn-sm" title="Kartu Stok">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <a href="{{ route('products.edit', $p) }}"
                                   class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $p) }}"
                                      method="POST" class="d-inline"
                                      data-name="{{ $p->name }}"
                                      onsubmit="return confirm('Hapus / nonaktifkan ' + this.dataset.name + '?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Tidak ada barang yang cocok</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $items->links() }}
        </div>

    </div>
</div>

@endsection