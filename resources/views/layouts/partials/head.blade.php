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
<link rel="stylesheet" href="/assets/css/sidebar.css" />
<link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

<!-- Third Party CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Helpers & Config -->
<script src="/assets/vendor/js/helpers.js"></script>
<script src="/assets/js/config.js"></script>

@stack('styles')