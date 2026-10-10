@extends('layouts.gudang')

@section('title', 'Satuan')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-rulers text-primary"></i>
                Daftar Satuan
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('units.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Satuan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Satuan</th>
                        <th>Simbol</th>
                        <th class="text-center" width="120">Jumlah Barang</th>
                        <th class="text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $u)
                        <tr>
                            <td class="text-muted">{{ $items->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td><span class="badge bg-dark">{{ $u->symbol }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $u->products_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('units.edit', $u) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('units.destroy', $u) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus satuan {{ $u->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada satuan</div>
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