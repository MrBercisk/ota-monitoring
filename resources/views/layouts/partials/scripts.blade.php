<!-- Core JS -->
<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/js/helpers.js"></script>
<script src="/assets/vendor/js/menu.js"></script>
<script src="/assets/js/main.js"></script>

<!-- Third Party JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/js/swal-helper.js"></script>
<script src="/assets/js/select2-helper.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="{{ asset('/assets/js/navbar.js') }}"></script>
<!-- Flash Messages -->
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