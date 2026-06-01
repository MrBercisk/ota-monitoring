// ==========================================
// GLOBAL SELECT2 HELPER
// ==========================================

document.addEventListener('DOMContentLoaded', function () {

    // Auto init .select2 (tanpa placeholder)
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
    });

    // Auto init .select2-placeholder (dengan placeholder & clear)
    $('.select2-placeholder').each(function () {
        $(this).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder') || '-- Pilih --',
            allowClear: true,
        });
    });

});