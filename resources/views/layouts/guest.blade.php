<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — SIMG SPPG Kuningan Jalaksana Maniskidul 3</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --navy:        #1e3a8a;
            --navy-dark:   #172e6b;
            --navy-light:  #eff6ff;
            --gold:        #f59e0b;
            --gold-hover:  #d97706;
            --text:        #0f172a;
            --text-muted:  #64748b;
            --border:      #e2e8f0;
        }

        * { box-sizing: border-box; }

        html, body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 14px;
            color: var(--text);
            margin: 0;
            min-height: 100vh;
        }

        /* ============================================================
           BACKGROUND
        ============================================================ */
        body {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 60%, #0f1e4d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ornamen background */
        body::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245,158,11,.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(96,165,250,.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* ============================================================
           AUTH CARD
        ============================================================ */
        .auth-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.4);
        }

        /* Sisi kiri — branding */
        .auth-brand {
            background: linear-gradient(150deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: #fff;
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(245,158,11,.25) 0%, transparent 70%);
            border-radius: 50%;
        }

        .auth-brand-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            background: rgba(255,255,255,.08);
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .auth-brand h1 {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.3;
            margin: 0 0 8px 0;
            letter-spacing: -0.3px;
        }
        .auth-brand .subtitle {
            font-size: 13px;
            color: rgba(255,255,255,.7);
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .auth-brand .instansi {
            font-size: 12.5px;
            color: rgba(255,255,255,.85);
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,.15);
            font-weight: 500;
            line-height: 1.6;
        }
        .auth-brand .instansi strong {
            display: block;
            color: #fff;
            font-size: 13.5px;
            margin-bottom: 4px;
        }

        /* Sisi kanan — form */
        .auth-form {
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.4px;
            margin: 0 0 6px 0;
        }
        .auth-form .form-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 26px;
        }

        .form-label {
            font-weight: 600;
            font-size: 12.5px;
            color: var(--text);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-control {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all .15s;
        }
        .form-control:focus {
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .form-check-input:checked {
            background-color: var(--navy);
            border-color: var(--navy);
        }

        .btn-login {
            background: var(--navy);
            border: none;
            color: #fff;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.3px;
            width: 100%;
            transition: all .15s;
        }
        .btn-login:hover {
            background: var(--navy-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, .3);
        }

        .auth-footer {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            font-size: 12.5px;
            color: var(--text-muted);
        }
        .auth-footer a {
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }

        .input-group-text {
            background: var(--navy-light);
            border: 1.5px solid var(--border);
            border-right: none;
            color: var(--navy);
            font-size: 15px;
        }
        .input-group .form-control {
            border-left: none;
        }
        .input-group .form-control:focus {
            border-left: none;
        }

        .copyright {
            position: relative;
            z-index: 2;
            color: rgba(255,255,255,.55);
            font-size: 12px;
            text-align: center;
            margin-top: 24px;
            letter-spacing: 0.2px;
        }
        .copyright strong { color: rgba(255,255,255,.85); }

        @media (max-width: 860px) {
            .auth-wrapper {
                grid-template-columns: 1fr;
                max-width: 460px;
            }
            .auth-brand {
                padding: 30px 28px;
            }
            .auth-brand-logo {
                width: 64px;
                height: 64px;
                margin-bottom: 16px;
            }
            .auth-brand h1 { font-size: 17px; }
            .auth-form { padding: 32px 26px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div>
        <div class="auth-wrapper">

            {{-- SISI KIRI: BRANDING --}}
            <div class="auth-brand">
                <div>
                    <img src="{{ asset('logo-bgn.png') }}"
                         alt="Logo Badan Gizi Nasional"
                         class="auth-brand-logo"
                         onerror="this.style.display='none'">

                    <h1>Sistem Informasi<br>Manajemen Gudang</h1>
                    <p class="subtitle">
                        Kelola stok bahan pangan, pantau pergerakan barang,
                        dan pastikan distribusi gizi berjalan lancar.
                    </p>
                </div>

                <div class="instansi">
                    <strong>SPPG Kuningan Jalaksana Maniskidul 3</strong>
                    Badan Gizi Nasional — Republik Indonesia
                </div>
            </div>

            {{-- SISI KANAN: FORM --}}
            <div class="auth-form">
                {{ $slot }}
            </div>

        </div>

        <div class="copyright">
            © {{ date('Y') }} <strong>Badan Gizi Nasional</strong> — SPPG Kuningan Jalaksana Maniskidul 3
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>