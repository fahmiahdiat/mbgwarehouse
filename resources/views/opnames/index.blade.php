@extends('layouts.gudang')

@section('title', 'Stock Opname')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-clipboard-check text-primary"></i>
                Daftar Stock Opname
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
            <a href="{{ route('opnames.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Stock Opname
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="180">Kode</th>
                        <th width="120">Tanggal</th>
                        <th class="text-center" width="100">Jumlah Item</th>
                        <th width="100">Status</th>
                        <th>Catatan</th>
                        <th width="130">User</th>
                        <th class="text-end" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $so)
                        <tr>
                            <td><span class="badge bg-dark">{{ $so->code }}</span></td>
                            <td>{{ $so->date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $so->details_count }}</span>
                            </td>
                            <td>{!! $so->status_badge !!}</td>
                            <td class="text-muted small">{{ Str::limit($so->notes, 40) ?? '—' }}</td>
                            <td><small>{{ $so->user?->name ?? '—' }}</small></td>
                            <td class="text-end">
                                <a href="{{ route('opnames.show', $so) }}"
                                   class="btn btn-outline-info btn-sm" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('opnames.destroy', $so) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus SO {{ $so->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada Stock Opname</div>
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