@extends('layouts.gudang')

@section('title', $item->exists ? 'Edit Satuan' : 'Tambah Satuan')

@section('content')

<div class="card" style="max-width: 600px;">
    <div class="card-body">

        <form method="POST"
              action="{{ $item->exists ? route('units.update', $item) : route('units.store') }}">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Nama Satuan <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $item->name) }}"
                       placeholder="Contoh: Kilogram" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Simbol <span class="text-danger">*</span></label>
                <input type="text" name="symbol"
                       class="form-control @error('symbol') is-invalid @enderror"
                       value="{{ old('symbol', $item->symbol) }}"
                       placeholder="Contoh: Kg" required>
                @error('symbol')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('units.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection