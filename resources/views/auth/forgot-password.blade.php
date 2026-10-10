<x-guest-layout>

    <h2>Lupa Password</h2>
    <p class="form-subtitle">
        Masukkan email akun Anda. Kami akan mengirimkan tautan untuk
        mereset password.
    </p>

    @if (session('status'))
        <div class="alert alert-success py-2 small mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Terdaftar</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email"
                       type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-envelope-check me-1"></i> Kirim Tautan Reset
        </button>

    </form>

    <div class="auth-footer">
        Ingat password Anda?
        <a href="{{ route('login') }}">Kembali ke Login</a>
    </div>

</x-guest-layout>