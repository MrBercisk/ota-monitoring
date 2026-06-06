@extends('layouts.app')

@section('title', 'Tambah Delay Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🗺️ Tambah Delay Kategori</h5>
                <a href="{{ route('delay-category.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('delay-category.update', $delayCategory) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text"
                            name="category_name"
                            class="form-control @error('category_name') is-invalid @enderror"
                            value="{{ old('category_name', $delayCategory->name) }}"
                            placeholder="cth: Damage to Aircraft">
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Simpan
                        </button>
                        <a href="{{ route('delay-category.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection