@extends('layouts.gudang')

@php
    $meta = [
        'in' => ['title' => 'Barang Masuk', 'icon' => 'bi-box-arrow-in-down', 'color' => 'success'],
        'out' => ['title' => 'Barang Keluar', 'icon' => 'bi-box-arrow-up', 'color' => 'danger'],
        'return_supplier' => ['title' => 'Retur ke Supplier', 'icon' => 'bi-arrow-return-left', 'color' => 'warning'],
        'return_customer' => ['title' => 'Retur ke Gudang', 'icon' => 'bi-arrow-return-right', 'color' => 'info'],
    ];
    $m = $meta[$type];
    $isIncoming = in_array($type, ['in', 'return_supplier']);
    $isOutgoing = in_array($type, ['out', 'return_supplier']);

    $productsJson = $products->map(function ($p) {
        return [
            'id'        => $p->id,
            'code'      => $p->code,
            'name'      => $p->name,
            'stock'     => (int) $p->stock,
            'unit'      => $p->unit?->symbol ?? '',
            'has_batch' => (bool) $p->has_batch,
        ];
    })->values()->toJson();
@endphp

@section('title', 'Input ' . $m['title'])

@section('content')

<form method="POST" action="{{ route('transactions.store', $type) }}">
    @csrf

    {{-- HEADER --}}
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">
                <i class="bi {{ $m['icon'] }} text-{{ $m['color'] }}"></i>
                Informasi {{ $m['title'] }}
            </h6>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($isIncoming)
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">— Pilih Supplier —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">No. Surat Jalan</label>
                        <input type="text" name="delivery_note" class="form-control"
                               value="{{ old('delivery_note') }}" placeholder="Contoh: SJ-2026-1001">
                    </div>
                @else
                    <div class="col-md-4">
                        <label class="form-label">Penerima / Bagian</label>
                        <input type="text" name="recipient" class="form-control"
                               value="{{ old('recipient') }}" placeholder="Contoh: Divisi Dapur">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Keperluan</label>
                        <input type="text" name="purpose" class="form-control"
                               value="{{ old('purpose') }}" placeholder="Contoh: Kebutuhan harian">
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="2" class="form-control"
                              placeholder="Opsional">{{ old('notes') }}</textarea>
                </div>
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
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()">
                    <i class="bi bi-plus-lg"></i> Tambah Baris
                </button>
            </div>

            @if($isOutgoing)
                <div class="alert alert-info py-2 mb-3">
                    <i class="bi bi-info-circle"></i>
                    <small>
                        Barang dengan <strong>batch</strong> akan otomatis diambil dengan metode
                        <strong>FEFO</strong> (First Expired First Out).
                    </small>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 32%;">Barang <span class="text-danger">*</span></th>
                            <th style="width: 10%;">Qty <span class="text-danger">*</span></th>
                            <th style="width: 14%;">
                                Tgl Expired
                                <i class="bi bi-info-circle text-muted"
                                   title="Hanya untuk barang dengan tracking batch"
                                   style="font-size: 11px;"></i>
                            </th>
                            <th style="width: 12%;">Harga Beli</th>
                            <th>Keterangan</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>

            <div class="text-muted small mt-2">
                <i class="bi bi-info-circle"></i>
                Kolom <strong>Tgl Expired</strong> hanya aktif untuk barang yang memiliki tracking batch
                (bahan pokok, minuman). Barang lain otomatis di-nonaktifkan.
            </div>
        </div>
    </div>

    {{-- ACTION --}}
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-{{ $m['color'] }}">
            <i class="bi bi-save"></i> Simpan {{ $m['title'] }}
        </button>
        <a href="{{ route('transactions.index', $type) }}" class="btn btn-secondary">
            <i class="bi bi-x-lg"></i> Batal
        </a>
    </div>

</form>

@endsection

@push('scripts')
<script>
    const PRODUCTS    = {!! $productsJson !!};
    const IS_OUTGOING = {{ $isOutgoing ? 'true' : 'false' }};

    let rowIndex = 0;

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');

        let optionsHtml = '<option value="">— Pilih Barang —</option>';
        PRODUCTS.forEach(function (p) {
            optionsHtml += '<option value="' + p.id + '" ' +
                           'data-stock="' + p.stock + '" ' +
                           'data-unit="' + p.unit + '" ' +
                           'data-has-batch="' + (p.has_batch ? '1' : '0') + '">' +
                           p.code + ' — ' + p.name + ' (stok: ' + p.stock + ' ' + p.unit + ')' +
                           '</option>';
        });

        tr.innerHTML =
            // Barang
            '<td>' +
                '<select name="items[' + rowIndex + '][product_id]" ' +
                        'class="form-select form-select-sm product-select" ' +
                        'onchange="onProductChange(this)" required>' +
                    optionsHtml +
                '</select>' +
                '<div class="small text-muted mt-1 product-info"></div>' +
            '</td>' +

            // Qty
            '<td>' +
                '<input type="number" name="items[' + rowIndex + '][qty]" ' +
                       'class="form-control form-control-sm qty-input" ' +
                       'min="1" value="1" required oninput="validateQty(this)">' +
            '</td>' +

            // Tgl Expired — selalu ada, tapi disabled untuk non-batch
            '<td>' +
                '<input type="date" name="items[' + rowIndex + '][expired_date]" ' +
                       'class="form-control form-control-sm expired-input" ' +
                       'title="Hanya untuk barang dengan tracking batch">' +
            '</td>' +

            // Harga Beli — selalu aktif
            '<td>' +
                '<input type="number" name="items[' + rowIndex + '][price]" ' +
                       'class="form-control form-control-sm" ' +
                       'min="0" step="100" placeholder="Rp">' +
            '</td>' +

            // Keterangan
            '<td>' +
                '<input type="text" name="items[' + rowIndex + '][notes]" ' +
                       'class="form-control form-control-sm" placeholder="Opsional">' +
            '</td>' +

            // Hapus
            '<td class="text-center">' +
                '<button type="button" class="btn btn-outline-danger btn-sm" ' +
                        'onclick="removeRow(this)"><i class="bi bi-x-lg"></i></button>' +
            '</td>';

        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeRow(btn) {
        const tbody = document.getElementById('itemsBody');
        if (tbody.rows.length <= 1) {
            alert('Minimal harus ada 1 barang.');
            return;
        }
        btn.closest('tr').remove();
    }

    function onProductChange(select) {
        const opt = select.options[select.selectedIndex];
        const tr  = select.closest('tr');
        const info = tr.querySelector('.product-info');
        const expiredInput = tr.querySelector('.expired-input');

        if (!opt.value) {
            info.textContent = '';
            disableExpired(expiredInput);
            return;
        }

        const stock    = opt.dataset.stock;
        const unit     = opt.dataset.unit;
        const hasBatch = opt.dataset.hasBatch === '1';

        info.innerHTML = '<i class="bi bi-box"></i> Stok tersedia: <strong>' + stock + ' ' + unit + '</strong>';

        if (hasBatch) {
            enableExpired(expiredInput);
        } else {
            disableExpired(expiredInput);
        }

        // Kalau ganti barang, validasi qty ulang
        validateQty(tr.querySelector('.qty-input'));
    }

    function enableExpired(input) {
        input.disabled = false;
        input.classList.remove('bg-light');
        input.title = 'Isi tanggal expired (opsional)';
    }

    function disableExpired(input) {
        input.disabled = true;
        input.value = ''; // reset value
        input.classList.add('bg-light');
        input.title = 'Tidak perlu diisi untuk barang ini';
    }

    function validateQty(input) {
        const tr = input.closest('tr');
        const select = tr.querySelector('.product-select');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;

        const stock = parseInt(opt.dataset.stock) || 0;
        const qty = parseInt(input.value) || 0;

        if (IS_OUTGOING && qty > stock) {
            input.classList.add('is-invalid');
            let fb = tr.querySelector('.qty-feedback');
            if (!fb) {
                fb = document.createElement('div');
                fb.className = 'invalid-feedback qty-feedback';
                input.parentNode.appendChild(fb);
            }
            fb.textContent = 'Stok tidak cukup. Tersedia: ' + stock + '.';
        } else {
            input.classList.remove('is-invalid');
            const fb = tr.querySelector('.qty-feedback');
            if (fb) fb.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', addRow);
</script>
@endpush