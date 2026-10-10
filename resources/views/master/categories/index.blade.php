@extends('layouts.gudang')

@section('title', 'Kategori')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-tags text-primary"></i>
                Daftar Kategori
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </a>
        </div>

        <form method="GET" class="mb-3">
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama kategori..."
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center" width="120">Jumlah Barang</th>
                        <th class="text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $c)
                        <tr>
                            <td class="text-muted">{{ $items->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $c->name }}</td>
                            <td class="text-muted">{{ $c->description ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $c->products_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('categories.edit', $c) }}"
                                   class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $c) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori {{ $c->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada kategori</div>
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