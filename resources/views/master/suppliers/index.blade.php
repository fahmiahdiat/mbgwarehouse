@extends('layouts.gudang')

@section('title', 'Supplier')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-truck text-primary"></i>
                Daftar Supplier
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Supplier
            </a>
        </div>

        <form method="GET" class="mb-3">
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari supplier..."
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
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
                        <th>Nama Supplier</th>
                        <th>Kontak</th>
                        <th>PIC</th>
                        <th>Alamat</th>
                        <th class="text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $s)
                        <tr>
                            <td class="text-muted">{{ $items->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $s->name }}</td>
                            <td>
                                @if($s->phone)<div><i class="bi bi-telephone text-muted"></i> {{ $s->phone }}</div>@endif
                                @if($s->email)<div><i class="bi bi-envelope text-muted"></i> {{ $s->email }}</div>@endif
                                @if(!$s->phone && !$s->email)<span class="text-muted">—</span>@endif
                            </td>
                            <td>{{ $s->pic ?? '—' }}</td>
                            <td class="text-muted small">{{ Str::limit($s->address, 40) ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('suppliers.edit', $s) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $s) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus supplier {{ $s->name }}?')">
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
                                <div class="mt-2">Belum ada supplier</div>
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