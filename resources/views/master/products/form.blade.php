@extends('layouts.gudang')

@section('title', $item->exists ? 'Edit Barang' : 'Tambah Barang')

@section('content')

<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ $item->exists ? route('products.update', $item) : route('products.store') }}">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="row g-3">

                {{-- Kode --}}
                <div class="col-md-4">
                    <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $item->code) }}"
                           placeholder="Contoh: BRG001" required autofocus>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nama --}}
                <div class="col-md-8">
                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $item->name) }}"
                           placeholder="Contoh: Beras Premium" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Kategori --}}
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}"
                                @selected(old('category_id', $item->category_id) == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Satuan --}}
                <div class="col-md-4">
                    <label class="form-label">Satuan</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                        <option value="">— Pilih Satuan —</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}"
                                @selected(old('unit_id', $item->unit_id) == $u->id)>
                                {{ $u->name }} ({{ $u->symbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Supplier --}}
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">— Pilih Supplier —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}"
                                @selected(old('supplier_id', $item->supplier_id) == $s->id)>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Lokasi --}}
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                        <option value="">— Pilih Lokasi —</option>
                        @foreach($locations as $l)
                            <option value="{{ $l->id }}"
                                @selected(old('location_id', $item->location_id) == $l->id)>
                                {{ $l->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Min Stock --}}
                <div class="col-md-4">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="min_stock" min="0"
                           class="form-control @error('min_stock') is-invalid @enderror"
                           value="{{ old('min_stock', $item->min_stock ?? 0) }}">
                    <small class="text-muted">Batas peringatan stok menipis</small>
                    @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active"   @selected(old('status', $item->status) == 'active')>Aktif</option>
                        <option value="inactive" @selected(old('status', $item->status) == 'inactive')>Nonaktif</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Batch & Expired</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="has_batch" id="has_batch" value="1"
                            class="form-check-input"
                            @checked(old('has_batch', $item->has_batch))>
                        <label class="form-check-label" for="has_batch">
                            Barang ini butuh tracking <strong>Batch</strong> & <strong>Expired</strong>
                        </label>
                    </div>
                    <small class="text-muted">Centang untuk makanan, minuman, obat, kosmetik</small>
                </div>

                {{-- Keterangan --}}
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Keterangan opsional">{{ old('description', $item->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            {{-- Info stok saat ini (kalau edit) --}}
            @if($item->exists)
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Stok saat ini: <strong>{{ $item->stock }}</strong> {{ $item->unit?->symbol }}.
                    Stok hanya bisa diubah lewat <strong>Barang Masuk</strong>, <strong>Barang Keluar</strong>,
                    <strong>Stock Opname</strong>, atau <strong>Penyesuaian Stok</strong>.
                </div>
            @endif

            <hr class="my-3">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection