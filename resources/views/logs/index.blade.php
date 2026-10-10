@extends('layouts.gudang')

@section('title', 'Riwayat Aktivitas')

@section('content')

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-clock-history text-primary"></i>
                Riwayat Aktivitas
                <span class="badge bg-secondary ms-1">{{ $items->total() }}</span>
            </h6>
        </div>

        <form method="GET" class="mb-3">
            <div class="input-group input-group-sm" style="max-width: 350px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari aktivitas..."
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="160">Waktu</th>
                        <th width="150">User</th>
                        <th width="180">Aksi</th>
                        <th>Deskripsi</th>
                        <th width="180">Referensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $l)
                        <tr>
                            <td class="small">
                                <i class="bi bi-clock text-muted"></i>
                                {{ $l->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <i class="bi bi-person-circle text-muted"></i>
                                <small>{{ $l->user?->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $l->action }}</span>
                            </td>
                            <td class="small">{{ $l->description }}</td>
                            <td class="small">
                                @if($l->reference)
                                    <span class="badge bg-dark">{{ $l->reference }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 32px; opacity: .3;"></i>
                                <div class="mt-2">Belum ada aktivitas</div>
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