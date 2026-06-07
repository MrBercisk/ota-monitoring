<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">

    {{-- Hamburger mobile --}}
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0" href="javascript:void(0)">
            <i class="icon-base ri ri-menu-line icon-md"></i>
        </a>
    </div>

    {{-- WRAPPER: kiri + kanan dalam satu flex row full width --}}
    <div class="d-flex align-items-center w-100 gap-3">

    {{-- Kiri: Page title + breadcrumb --}}
    <div class="d-none d-xl-flex align-items-center gap-2" style="flex: 1 1 auto; min-width: 0;">

        {{-- Icon toggle KHUSUS desktop — TIDAK pakai class layout-menu-toggle --}}
        <a href="javascript:void(0)"
        id="desktopMenuToggle"
        class="nav-link d-flex align-items-center justify-content-center me-1"
        style="width:34px; height:34px; border-radius:8px; color: var(--bs-secondary-color, #697a8d); flex-shrink:0;"
        title="Toggle Menu">
          <i class="ri ri-menu-fold-line" style="font-size:18px;"></i>
        </a>

        {{-- Page title + breadcrumb --}}
        <div class="d-flex flex-column justify-content-center" style="min-width:0;">
            <h6 class="navbar-page-title mb-0" id="navbar-page-title">Dashboard</h6>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb navbar-breadcrumb mb-0" id="navbar-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">OTA Monitor</a></li>
                    <li class="breadcrumb-item active" id="breadcrumb-current">Dashboard</li>
                </ol>
            </nav>
        </div>

    </div>

        {{-- Kanan --}}
        <div class="d-flex align-items-center gap-3 ms-auto" style="flex: 0 0 auto;">
            <ul class="navbar-nav flex-row align-items-center gap-3 mb-0">

                {{-- Tanggal & Jam --}}
                <li class="nav-item d-none d-md-flex">
                    <div class="navbar-datetime">
                        <span class="navbar-date" id="navbar-date"></span>
                        <span class="navbar-time" id="navbar-time"></span>
                    </div>
                </li>

                {{-- Divider --}}
                <li class="nav-item d-none d-md-flex">
                    <div class="navbar-divider"></div>
                </li>

                {{-- Notifikasi --}}
                <li class="nav-item dropdown" id="notif-dropdown-li">
                    <a href="#" class="nav-link navbar-icon-btn dropdown-toggle hide-arrow"
                    id="notifDropdown" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false"
                    title="Notifikasi">
                        <i class="ri ri-notification-3-line"></i>
                        <span class="navbar-notif-badge d-none" id="notif-badge">0</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" aria-labelledby="notifDropdown">
                        {{-- Header --}}
                        <div class="notif-header d-flex align-items-center justify-content-between px-3 py-2">
                            <span class="notif-header-title">Notifikasi Hari Ini</span>
                            <span class="notif-header-sub" id="notif-count-label" style="font-size:11px;color:#b0b8c1;"></span>
                        </div>
                        <div class="dropdown-divider m-0"></div>

                        {{-- List --}}
                        <div class="notif-list" id="notif-list"></div>

                        {{-- Empty state --}}
                        <div class="notif-empty text-center py-4" id="notif-empty" style="display:none">
                            <i class="ri ri-notification-off-line" style="font-size:28px;color:#d0d5dd;"></i>
                            <p class="mt-1 mb-0" style="font-size:12px;color:#b0b8c1;">Tidak ada notifikasi</p>
                        </div>

                        {{-- Footer --}}
                        <div class="dropdown-divider m-0"></div>
                            <div class="notif-footer text-center py-2">
                                <a href="{{ route('reports.index') }}" class="notif-footer-link">
                                    Lihat Laporan OTA →
                                </a>
                            </div>
                        </div>
                </li>

                {{-- Divider --}}
                <li class="nav-item d-none d-md-flex">
                    <div class="navbar-divider"></div>
                </li>

                {{-- User Dropdown --}}
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0 d-flex align-items-center gap-2"
                       href="javascript:void(0);"
                       data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="/assets/img/avatars/1.png" alt="User Avatar" class="rounded-circle" />
                        </div>
                        <div class="d-none d-md-flex flex-column">
                            <span class="navbar-username">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <span class="navbar-userrole">Administrator</span>
                        </div>
                        <i class="ri ri-arrow-down-s-line navbar-caret d-none d-md-inline"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar">
                                        <img src="/assets/img/avatars/1.png" alt="User Avatar" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                        <small>{{ auth()->user()->email ?? '' }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><div class="dropdown-divider my-1"></div></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="ri ri-user-line me-2"></i> Profile
                            </a>
                        </li>
                        <li><div class="dropdown-divider my-1"></div></li>
                        <li>
                            <div class="d-grid px-2 pt-1 pb-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger d-flex w-100 justify-content-center align-items-center gap-2">
                                        <small>Logout</small>
                                        <i class="ri ri-logout-box-r-line ri-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

    </div>
</nav>

<script>
    window.NOTIF_ROUTE = "{{ route('notifications.index') }}";
    (function () {
    const STORAGE_KEY = 'sidebar-collapsed';
    const htmlEl = document.documentElement;
    const toggleBtn = document.getElementById('desktopMenuToggle');
    const COLLAPSED_CLASS = 'layout-menu-collapsed';

    // Hanya jalankan di desktop (≥1200px)
    function isDesktop() {
        return window.innerWidth >= 1200;
    }

    // Restore state
    if (isDesktop() && localStorage.getItem(STORAGE_KEY) === 'true') {
        htmlEl.classList.add(COLLAPSED_CLASS);
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // pastikan tidak bubble ke handler lain

            if (!isDesktop()) return; // abaikan jika mobile/tablet

            const isCollapsed = htmlEl.classList.toggle(COLLAPSED_CLASS);
            localStorage.setItem(STORAGE_KEY, isCollapsed);
        });
    }

    // Reset collapsed state saat resize ke mobile
    window.addEventListener('resize', function () {
        if (!isDesktop()) {
            htmlEl.classList.remove(COLLAPSED_CLASS);
        } else if (localStorage.getItem(STORAGE_KEY) === 'true') {
            htmlEl.classList.add(COLLAPSED_CLASS);
        }
    });
})();
</script>