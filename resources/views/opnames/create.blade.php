@extends('layouts.gudang')

@section('title', 'Buat Stock Opname')

@section('content')

<form method="POST" action="{{ route('opnames.store') }}" id="soForm">
    @csrf

    {{-- HEADER --}}
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">
                <i class="bi bi-clipboard-check text-primary"></i>
                Informasi Stock Opname
            </h6>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="date"
                           class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-9">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes"
                           class="form-control @error('notes') is-invalid @enderror"
                           value="{{ old('notes') }}"
                           placeholder="Contoh: SO Akhir Bulan Oktober 2026">
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle"></i>
                Isi kolom <strong>Stok Fisik</strong> dengan jumlah aktual di gudang.
                Sistem akan otomatis menghitung <strong>selisih</strong> dengan stok sistem.
                <br>
                <small>Kosongkan kolom fisik jika barang tidak ikut dihitung pada opname ini.</small>
            </div>
        </div>
    </div>

    {{-- DETAIL --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-list-ul text-primary"></i>
                    Detail Barang
                    <span class="badge bg-secondary ms-1">{{ $products->count() }} barang</span>
                </h6>
                <input type="text" id="search" class="form-control form-control-sm"
                       style="max-width: 260px;"
                       placeholder="Cari barang..."
                       onkeyup="filterRows(this.value)">
            </div>

            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle" id="tbl">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th width="110">Kode</th>
                            <th>Nama Barang</th>
                            <th class="text-end" width="120">Stok Sistem</th>
                            <th style="width: 160px;">Stok Fisik</th>
                            <th class="text-end" width="100">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr data-name="{{ strtolower($p->code . ' ' . $p->name) }}">
                                <td><span class="badge bg-dark">{{ $p->code }}</span></td>
                                <td>
                                    {{ $p->name }}
                                    <small class="text-muted">({{ $p->unit?->symbol }})</small>
                                </td>
                                <td class="text-end">
                                    <span class="stock-system" data-stock="{{ $p->stock }}">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number" min="0"
                                           name="physical[{{ $p->id }}]"
                                           class="form-control form-control-sm physical-input"
                                           data-stock="{{ $p->stock }}"
                                           value="{{ $p->stock }}"
                                           oninput="computeDiff(this)">
                                </td>
                                <td class="text-end">
                                    <span class="diff-badge badge bg-secondary">0</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ACTION --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan Stock Opname
        </button>
        <a href="{{ route('opnames.index') }}" class="btn btn-secondary">
            <i class="bi bi-x-lg"></i> Batal
        </a>
    </div>

</form>

@endsection

@push('scripts')
<script>
    /**
     * Hitung selisih real-time.
     */
    function computeDiff(input) {
        const systemStock = parseInt(input.dataset.stock) || 0;
        const physical    = parseInt(input.value);
        const tr          = input.closest('tr');
        const badge       = tr.querySelector('.diff-badge');

        if (isNaN(physical)) {
            badge.className = 'diff-badge badge bg-secondary';
            badge.textContent = '0';
            return;
        }

        const diff = physical - systemStock;

        if (diff === 0) {
            badge.className = 'diff-badge badge bg-secondary';
            badge.textContent = '0';
        } else if (diff > 0) {
            badge.className = 'diff-badge badge bg-success';
            badge.textContent = '+' + diff;
        } else {
            badge.className = 'diff-badge badge bg-danger';
            badge.textContent = diff;
        }
    }

    /**
     * Filter baris berdasarkan pencarian.
     */
    function filterRows(q) {
        q = q.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(function (tr) {
            tr.style.display = tr.dataset.name.includes(q) ? '' : 'none';
        });
    }

    // Init: hitung semua selisih saat halaman dibuka
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.physical-input').forEach(computeDiff);
    });
</script>
@endpush