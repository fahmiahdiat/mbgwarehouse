@extends('layouts.gudang')

@section('title', $item->exists ? 'Edit Lokasi' : 'Tambah Lokasi')

@section('content')

<div class="card" style="max-width: 700px;">
    <div class="card-body">

        <form method="POST"
              action="{{ $item->exists ? route('locations.update', $item) : route('locations.store') }}">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Kode Lokasi</label>
                <input type="text" name="code"
                       class="form-control @error('code') is-invalid @enderror"
                       value="{{ old('code', $item->code) }}"
                       placeholder="Contoh: A1">
                <small class="text-muted">Opsional, untuk penanda rak/bin</small>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $item->name) }}"
                       placeholder="Contoh: Rak A1" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Keterangan posisi/letak">{{ old('description', $item->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection