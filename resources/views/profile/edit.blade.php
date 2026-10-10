@extends('layouts.gudang')

@section('title', 'Profil Saya')

@section('content')

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="bi bi-person-circle text-primary"></i>
                    Informasi Profil
                </h6>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr>
                    <h6 class="mb-3 text-muted">
                        <i class="bi bi-shield-lock"></i>
                        Ubah Password
                        <small>(kosongkan jika tidak ingin mengubah)</small>
                    </h6>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               autocomplete="new-password">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 64px; color: #94a3b8;" class="mb-2">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h6 class="mb-1">{{ $user->name }}</h6>
                <small class="text-muted">{{ $user->email }}</small>
                <hr>
                <div class="small text-muted">
                    <div class="mb-2">
                        <i class="bi bi-shield-check"></i> Admin Gudang
                    </div>
                    <div>
                        Bergabung sejak<br>
                        <strong>{{ $user->created_at->format('d F Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection