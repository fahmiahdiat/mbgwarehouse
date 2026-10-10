@extends('layouts.gudang')

@section('title', $item->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')

<div class="card" style="max-width: 600px;">
    <div class="card-body">

        <form method="POST"
              action="{{ $item->exists ? route('categories.update', $item) : route('categories.store') }}">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $item->name) }}"
                       placeholder="Contoh: Bahan Pokok" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Deskripsi opsional">{{ old('description', $item->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection