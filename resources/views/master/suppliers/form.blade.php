@extends('layouts.gudang')

@section('title', $item->exists ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')

<div class="card">
    <div class="card-body">
        <form method="POST"
              action="{{ $item->exists ? route('suppliers.update', $item) : route('suppliers.store') }}">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $item->name) }}"
                           placeholder="Contoh: PT Sumber Pangan" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">PIC (Nama Kontak)</label>
                    <input type="text" name="pic"
                           class="form-control @error('pic') is-invalid @enderror"
                           value="{{ old('pic', $item->pic) }}"
                           placeholder="Contoh: Andi Wijaya">
                    @error('pic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $item->phone) }}"
                           placeholder="Contoh: 021-5550001">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $item->email) }}"
                           placeholder="Contoh: sales@perusahaan.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="3"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="Alamat lengkap supplier">{{ old('address', $item->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-3">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection