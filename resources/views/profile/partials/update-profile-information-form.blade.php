@if (session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Profile berhasil diperbarui.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('profile.update') }}">
    @csrf @method('PATCH')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    Email belum terverifikasi.
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 align-baseline">
                            Kirim ulang verifikasi
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="ri ri-save-line me-1"></i> Simpan
            </button>
        </div>
    </div>
</form>