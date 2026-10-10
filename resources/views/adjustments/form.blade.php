@extends('layouts.gudang')

@section('title', 'Buat Penyesuaian Stok')

@section('content')

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="bi bi-sliders text-primary"></i>
            Form Penyesuaian Stok
        </h6>

        <div class="alert alert-warning py-2">
            <i class="bi bi-exclamation-triangle"></i>
            <small>
                Penyesuaian stok akan langsung mengubah stok barang. Gunakan dengan hati-hati.
                Untuk selisih stock opname, sebaiknya gunakan fitur <strong>Stock Opname</strong>.
            </small>
        </div>

        <form method="POST" action="{{ route('adjustments.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="date"
                       class="form-control @error('date') is-invalid @enderror"
                       value="{{ old('date', date('Y-m-d')) }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Barang <span class="text-danger">*</span></label>
                <select name="product_id" id="productSelect"
                        class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">— Pilih Barang —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}"
                                data-stock="{{ $p->stock }}"
                                data-unit="{{ $p->unit?->symbol }}"
                                @selected(old('product_id') == $p->id)>
                            {{ $p->code }} — {{ $p->name }}
                            (stok: {{ $p->stock }} {{ $p->unit?->symbol }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text" id="stockInfo">
                    Pilih barang untuk melihat stok saat ini.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Nilai Penyesuaian <span class="text-danger">*</span>
                </label>
                <input type="number" name="adjustment"
                       class="form-control @error('adjustment') is-invalid @enderror"
                       value="{{ old('adjustment') }}"
                       placeholder="Contoh: -5 (kurangi) atau 3 (tambah)" required>
                @error('adjustment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">
                    Gunakan angka <strong>negatif</strong> untuk mengurangi stok, <strong>positif</strong> untuk menambah.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alasan <span class="text-danger">*</span></label>
                <input type="text" name="reason"
                       class="form-control @error('reason') is-invalid @enderror"
                       value="{{ old('reason') }}"
                       placeholder="Contoh: Barang rusak / Kemasan bocor / Selisih SO"
                       required>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Referensi</label>
                <input type="text" name="reference"
                       class="form-control @error('reference') is-invalid @enderror"
                       value="{{ old('reference') }}"
                       placeholder="Contoh: SO-20261008-001 (opsional)">
                @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr class="my-3">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Penyesuaian
                </button>
                <a href="{{ route('adjustments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('productSelect');
        const info   = document.getElementById('stockInfo');

        function updateInfo() {
            const opt = select.options[select.selectedIndex];
            if (!opt.value) {
                info.textContent = 'Pilih barang untuk melihat stok saat ini.';
                return;
            }
            const stock = opt.dataset.stock;
            const unit  = opt.dataset.unit;
            info.innerHTML = 'Stok saat ini: <strong>' + stock + ' ' + unit + '</strong>';
        }

        select.addEventListener('change', updateInfo);
        updateInfo();
    });
</script>
@endpush