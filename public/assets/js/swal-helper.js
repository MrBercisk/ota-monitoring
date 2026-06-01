// ==========================================
// GLOBAL SWEETALERT HELPERS
// ==========================================

// Toast mixin
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
});

// Toast shortcuts
window.swalSuccess = (message) => Toast.fire({ icon: 'success', title: message });
window.swalError   = (message) => Toast.fire({ icon: 'error',   title: message, timer: 3000 });
window.swalWarning = (message) => Toast.fire({ icon: 'warning', title: message });
window.swalInfo    = (message) => Toast.fire({ icon: 'info',    title: message });

// Konfirmasi hapus
window.swalDelete = (form, message = 'Data yang dihapus tidak bisa dikembalikan!') => {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
};

// Konfirmasi aksi umum
window.swalConfirm = (title, message, callback) => {
    Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) callback();
    });
};

// Auto bind .btn-delete
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const message = this.dataset.message || 'Data yang dihapus tidak bisa dikembalikan!';
            swalDelete(form, message);
        });
    });
});