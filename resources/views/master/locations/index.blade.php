@extends('layouts.gudang')

@section('title', 'Lokasi')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-geo-alt text-primary"></i>
                Daftar Lokasi
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('locations.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Lokasi
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="100">Kode</th>
                        <th>Nama Lokasi</th>
                        <th>Deskripsi</th>
                        <th class="text-center" width="120">Jumlah Barang</th>
                        <th class="text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $l)
                        <tr>
                            <td class="text-muted">{{ $items->firstItem() + $i }}</td>
                            <td>
                                @if($l->code)
                                    <span class="badge bg-dark">{{ $l->code }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $l->name }}</td>
                            <td class="text-muted small">{{ $l->description ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $l->products_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('locations.edit', $l) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('locations.destroy', $l) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus lokasi {{ $l->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada lokasi</div>
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