$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('body'),
    });

    $('.select2-placeholder').each(function () {
        $(this).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder') || '-- Pilih --',
            allowClear: true,
            dropdownParent: $('body'),
        });
    });

    // Force dropdown ke atas untuk semua select2
    $(document).on('select2:open', function() {
        document.querySelector('.select2-search__field')?.focus();
    });
});