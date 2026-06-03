@extends('layouts.app')
@section('title', 'Edit Jadwal Penerbangan')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✏️ Edit Jadwal Penerbangan</h5>
                <a href="{{ route('flight-schedule.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('flight-schedule.update', $flightSchedule) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nomor Penerbangan <span class="text-danger">*</span></label>
                        <input type="text"
                               name="flight_number"
                               class="form-control @error('flight_number') is-invalid @enderror"
                               value="{{ old('flight_number', $flightSchedule->flight_number) }}"
                               placeholder="cth: GA-101"
                               style="text-transform: uppercase">
                        @error('flight_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">STA <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="sta"
                                   class="form-control @error('sta') is-invalid @enderror"
                                   value="{{ old('sta', $flightSchedule->sta) }}">
                            @error('sta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">STD <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="std"
                                   class="form-control @error('std') is-invalid @enderror"
                                   value="{{ old('std', $flightSchedule->std) }}">
                            @error('std')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Update
                        </button>
                        <a href="{{ route('flight-schedule.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection