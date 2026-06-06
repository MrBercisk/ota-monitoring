<p class="text-muted">Setelah akun dihapus, semua data akan dihapus permanen.</p>

<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
    <i class="ri ri-delete-bin-line me-1"></i> Hapus Akun
</button>

{{-- Modal Konfirmasi --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p>Masukkan password untuk konfirmasi penghapusan akun.</p>
                    <input type="password"
                           name="password"
                           class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Password">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>