<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="sidebar-toggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>

        <img src="{{ asset('logo-bgn.png') }}"
             alt="Logo BGN"
             class="topbar-logo"
             onerror="this.style.display='none'">

        <div class="topbar-title">
            <strong>Sistem Informasi Manajemen Gudang</strong>
            <small>SPPG Kuningan Jalaksana Maniskidul 3 — Badan Gizi Nasional</small>
        </div>
    </div>

    <div class="topbar-right">
        <span class="topbar-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->locale('id')->isoFormat('ddd, D MMM Y') }}
        </span>

        <div class="dropdown">
            <button class="btn-user dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
                <span>{{ auth()->user()->name ?? 'Admin' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold small">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size: 11.5px;">
                        {{ auth()->user()->email }}
                    </div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-gear me-2"></i> Profil Saya
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logs.index') }}">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Aktivitas
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>