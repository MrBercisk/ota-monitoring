<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="/assets/"
      data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OTA Monitoring')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Helpers & Config -->
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar / Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ url('/') }}" class="app-brand-link">
                        <span class="app-brand-logo demo me-1">
                            <span class="text-primary">
                                <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z" fill="currentColor" />
                                    <path opacity="0.077704" fill-rule="evenodd" clip-rule="evenodd" d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z" fill="black" />
                                    <path opacity="0.077704" fill-rule="evenodd" clip-rule="evenodd" d="M0 65.2656L60.4839 99.0795V119.859L0 65.2656Z" fill="black" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z" fill="currentColor" />
                                    <path opacity="0.077704" fill-rule="evenodd" clip-rule="evenodd" d="M250 65.2656L189.516 99.8897V135.006L250 65.2656Z" fill="black" />
                                    <path opacity="0.077704" fill-rule="evenodd" clip-rule="evenodd" d="M250 65.2656L189.516 99.0497V120.886L250 65.2656Z" fill="black" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z" fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z" fill="white" fill-opacity="0.15" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z" fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z" fill="white" fill-opacity="0.3" />
                                </svg>
                            </span>
                        </span>
                        <span class="app-brand-text demo menu-text fw-semibold ms-2">OTA Monitor</span>
                    </a>

                    {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="menu-toggle-icon d-xl-inline-block align-middle"></i>
                    </a> --}}
                </div>

                <div class="menu-inner-shadow"></div>

               <ul class="menu-inner py-1">

                <li class="menu-item">
                    <a href="{{ route('dashboard')  }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-home-line"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('flights.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-flight-takeoff-line"></i>
                        <div>Data Penerbangan</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('stations.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-bar-chart-line"></i>
                        <div>Station</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('delay.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-error-warning-line"></i>
                        <div>Delay Code</div>
                    </a>
                </li>

                {{-- <li class="menu-item">
                    <a href="{{ url('/users') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-user-line"></i>
                        <div>Users</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ url('/settings') }}" class="menu-link">
                        <i class="menu-icon tf-icons ri ri-settings-4-line"></i>
                        <div>Settings</div>
                    </a>
                </li> --}}

            </ul>
            </aside>
            <!-- / Sidebar / Menu -->

            <!-- Layout page -->
            <div class="layout-page">

                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base ri ri-menu-line icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="icon-base ri ri-search-line icon-lg lh-0"></i>
                                <input
                                    type="text"
                                    class="form-control border-0 shadow-none"
                                    placeholder="Search..."
                                    aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0"
                                   href="javascript:void(0);"
                                   data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="/assets/img/avatars/1.png" alt="User Avatar" class="rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="/assets/img/avatars/1.png" alt="User Avatar" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                                    <small class="text-body-secondary">{{ auth()->user()->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="icon-base ri ri-user-line icon-md me-3"></i>
                                            <span>My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="icon-base ri ri-settings-4-line icon-md me-3"></i>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <div class="d-grid px-4 pt-2 pb-1">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger d-flex w-100 justify-content-center align-items-center">
                                                    <small class="align-middle">Logout</small>
                                                    <i class="ri ri-logout-box-r-line ms-2 ri-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- /Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        {{-- Flash Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @yield('content')

                    </div>

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    &copy; <script>document.write(new Date().getFullYear())</script> OTA Monitoring. All rights reserved.
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- /Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- /Content wrapper -->

            </div>
            <!-- /Layout page -->
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- /Layout wrapper -->

    <!-- Core JS -->
    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>
    <script src="/assets/js/main.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/swal-helper.js"></script>
    <script src="/assets/js/select2-helper.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Flash Message dari Session -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                swalSuccess(@json(session('success')));
            @endif
            @if(session('error'))
                swalError(@json(session('error')));
            @endif
            @if(session('warning'))
                swalWarning(@json(session('warning')));
            @endif
            @if(session('info'))
                swalInfo(@json(session('info')));
            @endif
        });
</script>

    @stack('scripts')
</body>
</html>