@extends('layouts.app')

@section('title', 'Tambah Station')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🗺️ Tambah Station</h5>
                <a href="{{ route('stations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('stations.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Kode Station <span class="text-danger">*</span></label>
                        <input type="text"
                               name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}"
                               placeholder="cth: CGK, SUB, DPS"
                               maxlength="10"
                               style="text-transform: uppercase">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kode IATA bandara, maks 10 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Station <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="cth: Soekarno-Hatta"
                               maxlength="100">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Simpan
                        </button>
                        <a href="{{ route('stations.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection