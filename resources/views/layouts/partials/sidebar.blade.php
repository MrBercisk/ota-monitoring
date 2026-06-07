<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo_ota.png') }}"
                    alt="OTA Logo"
                    style="height: 40px; width: auto;">
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2">Flight Data Management</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- Dashboard --}}
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ri ri-home-line"></i>
                <div>Dashboard</div>
            </a>
        </li>

        {{-- Penerbangan --}}
        <li class="menu-item {{ request()->routeIs('flights.*', 'flight-schedule.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri ri-flight-takeoff-line"></i>
                <div>Penerbangan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('flights.*') ? 'active' : '' }}">
                    <a href="{{ route('flights.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-flight-land-line"></i>
                        <div>Data Penerbangan</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('flight-schedule.*') ? 'active' : '' }}">
                    <a href="{{ route('flight-schedule.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-calendar-schedule-line"></i>
                        <div>Jadwal Penerbangan</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Master Data --}}
        <li class="menu-item {{ request()->routeIs('stations.*', 'delay.*', 'delay-category.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri ri-database-2-line"></i>
                <div>Master Data</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('stations.*') ? 'active' : '' }}">
                    <a href="{{ route('stations.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-map-pin-line"></i>
                        <div>Station</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('delay.*') ? 'active' : '' }}">
                    <a href="{{ route('delay.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-error-warning-line"></i>
                        <div>Delay Code</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('delay-category.*') ? 'active' : '' }}">
                    <a href="{{ route('delay-category.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-price-tag-3-line"></i>
                        <div>Delay Category</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Laporan --}}
        <li class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a href="{{ route('reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ri ri-file-chart-line"></i>
                <div>Laporan OTA</div>
            </a>
        </li>

        {{-- Pengaturan --}}
        <li class="menu-item {{ request()->routeIs('user.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri ri-settings-4-line"></i>
                <div>Pengaturan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-user-line"></i>
                        <div>User Management</div>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
    
</aside>