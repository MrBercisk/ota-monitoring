@if (session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Password berhasil diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf @method('PUT')

    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
            <input type="password"
                   name="current_password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
            <input type="password"
                   name="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control">
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="ri ri-lock-line me-1"></i> Update Password
            </button>
        </div>
    </div>
</form>