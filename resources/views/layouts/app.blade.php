<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="/assets/"
      data-template="vertical-menu-template-free">
<head>
    @include('layouts.partials.head')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            @include('layouts.partials.sidebar')

            {{-- Layout page --}}
            <div class="layout-page">

                {{-- Navbar --}}
                @include('layouts.partials.navbar')

                {{-- Content wrapper --}}
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

                    {{-- Footer --}}
                    @include('layouts.partials.footer')

                    <div class="content-backdrop fade"></div>
                </div>
                {{-- /Content wrapper --}}

            </div>
            {{-- /Layout page --}}

        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    {{-- /Layout wrapper --}}

    {{-- Scripts --}}
    @include('layouts.partials.scripts')
</body>
</html>