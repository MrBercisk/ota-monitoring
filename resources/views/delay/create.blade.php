@extends('layouts.app')

@section('title', 'Tambah Delay Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🗺️ Tambah Delay Code</h5>
                <a href="{{ route('delay.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('delay.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Delay Code <span class="text-danger">*</span></label>
                        <input type="text"
                               name="delay_code"
                               class="form-control @error('delay_code') is-invalid @enderror"
                               value="{{ old('delay_code') }}"
                               placeholder="cth: AS, AF, EO"
                               maxlength="2"
                               style="text-transform: uppercase">
                        @error('delay_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text"
                               name="reason"
                               class="form-control @error('reason') is-invalid @enderror"
                               value="{{ old('reason') }}"
                               placeholder="cth: Load Connection"
                               maxlength="100">
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="delay_category_id"
                                class="form-select select2-placeholder @error('delay_category_id') is-invalid @enderror"
                                data-placeholder="-- Pilih Category --">
                            <option value=""></option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('delay_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('delay_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Simpan
                        </button>
                        <a href="{{ route('delay.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection