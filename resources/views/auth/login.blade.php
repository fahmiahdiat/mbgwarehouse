<x-guest-layout>

    <h2>Masuk ke Sistem</h2>
    <p class="form-subtitle">Silakan masukkan kredensial akun Anda</p>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success py-2 small mb-3">
            {{ session('status') }}
        </div>
    @endif

    {{-- Error umum --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email"
                       type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password"
                       type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <button type="button"
                        class="btn btn-outline-secondary"
                        style="border: 1.5px solid var(--border); border-left: none;"
                        onclick="togglePassword()">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember"
                       class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="form-check-label small">
                    Ingat saya
                </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none fw-semibold">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
        </button>

    </form>

    <div class="auth-footer">
        <i class="bi bi-shield-lock"></i>
        Halaman ini hanya untuk petugas gudang berwenang
    </div>

    @push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
    @endpush

</x-guest-layout>