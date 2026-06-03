@extends('layouts.app')

@section('title', 'Input Data Penerbangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✈️ Input Data Penerbangan</h5>
                <a href="{{ route('flights.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('flights.store') }}">
                    @csrf

                    <div class="row g-3">

                        {{-- Tanggal & Flight Number --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penerbangan <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="flight_date"
                                   class="form-control @error('flight_date') is-invalid @enderror"
                                   value="{{ old('flight_date', date('Y-m-d')) }}">
                            @error('flight_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                     {{-- Nomor Penerbangan --}}
                        <div class="col-md-6">
                            <label class="form-label">Nomor Penerbangan <span class="text-danger">*</span></label>
                            <select name="flight_schedule_id"
                                    id="flight_schedule_id"
                                    class="form-select select2-placeholder @error('flight_schedule_id') is-invalid @enderror"
                                    data-placeholder="-- Pilih Nomor Penerbangan --">
                                <option value=""></option>
                                @foreach($flightSchedules as $fs)
                                    <option value="{{ $fs->id }}"
                                            data-sta="{{ $fs->sta }}"
                                            data-std="{{ $fs->std }}"
                                        {{ old('flight_schedule_id', $flight->flight_schedule_id ?? '') == $fs->id ? 'selected' : '' }}>
                                        {{ $fs->flight_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('flight_schedule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                       {{-- Station --}}
                        <div class="col-md-6">
                            <label class="form-label">Station <span class="text-danger">*</span></label>
                            <select name="station_id"
                                    class="form-select select2-placeholder @error('station_id') is-invalid @enderror"
                                    data-placeholder="-- Pilih Station --">
                                <option value=""></option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}"
                                        {{ old('station_id') == $station->id ? 'selected' : '' }}>
                                        {{ $station->code }} - {{ $station->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('station_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Delay Code --}}
                        <div class="col-md-6">
                            <label class="form-label">Delay Code</label>
                            <select name="delay_code_id"
                                    class="form-select select2-placeholder @error('delay_code_id') is-invalid @enderror"
                                    data-placeholder="-- Pilih Delay Code --">
                                <option value=""></option>
                                @php $currentCategory = '' @endphp
                                @foreach($delayCodes as $dc)
                                    @php $catName = $dc->category->name ?? 'Lainnya' @endphp
                                    @if($catName !== $currentCategory)
                                        @if($currentCategory !== '') </optgroup> @endif
                                        <optgroup label="{{ $catName }}">
                                        @php $currentCategory = $catName @endphp
                                    @endif
                                    <option value="{{ $dc->id }}"
                                        {{ old('delay_code_id', $flight->delay_code_id ?? '') == $dc->id ? 'selected' : '' }}>
                                        {{ $dc->code }} — {{ $dc->reason }}
                                    </option>
                                @endforeach
                                @if($currentCategory !== '') </optgroup> @endif
                            </select>
                            @error('delay_code_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Divider Jadwal --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <small class="text-muted fw-semibold">Jadwal</small>
                        </div>

                        {{-- STA & STD --}}
                        <div class="col-md-6">
                            <label class="form-label">STA (Scheduled Time Arrival) <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="sta"
                                   class="form-control @error('sta') is-invalid @enderror"
                                   value="{{ old('sta') }}">
                            @error('sta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">STD (Scheduled Time Departure) <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="std"
                                   class="form-control @error('std') is-invalid @enderror"
                                   value="{{ old('std') }}">
                            @error('std')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Divider Aktual --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <small class="text-muted fw-semibold">Aktual</small>
                        </div>

                        {{-- ATA & ATD --}}
                        <div class="col-md-6">
                            <label class="form-label">ATA (Actual Time Arrival)</label>
                            <input type="time"
                                   name="ata"
                                   class="form-control @error('ata') is-invalid @enderror"
                                   value="{{ old('ata') }}"
                                   id="ata">
                            @error('ata')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ATD (Actual Time Departure)</label>
                            <input type="time"
                                   name="atd"
                                   class="form-control @error('atd') is-invalid @enderror"
                                   value="{{ old('atd') }}">
                            @error('atd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview Delay --}}
                        <div class="col-12" id="delay-preview" style="display: none;">
                            <div class="alert alert-warning mb-0 py-2">
                                <i class="ri ri-time-line me-1"></i>
                                Estimasi delay: <strong id="delay-value">0</strong> menit
                            </div>
                        </div>

                        {{-- Divider Info --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <small class="text-muted fw-semibold">Keterangan</small>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <select name="remarks" class="form-select select2-placeholder @error('remarks') is-invalid @enderror">
                                <option value="">-- Pilih Remarks --</option>
                                <option value="night_stop" {{ old('remarks') == 'night_stop' ? 'selected' : '' }}>Night Stop</option>
                                <option value="cancel" {{ old('remarks') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                                <option value="divert" {{ old('remarks') == 'divert' ? 'selected' : '' }}>Divert</option>
                            </select>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Simpan
                            </button>
                            <a href="{{ route('flights.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview delay otomatis saat ATA diisi
    const staInput = document.querySelector('[name="sta"]');
    const ataInput = document.querySelector('[name="ata"]');
    const delayPreview = document.getElementById('delay-preview');
    const delayValue = document.getElementById('delay-value');

    function hitungDelay() {
        const sta = staInput.value;
        const ata = ataInput.value;

        if (!sta || !ata) {
            delayPreview.style.display = 'none';
            return;
        }

        const [staH, staM] = sta.split(':').map(Number);
        const [ataH, ataM] = ata.split(':').map(Number);
        const staMenit = staH * 60 + staM;
        const ataMenit = ataH * 60 + ataM;
        const diff = ataMenit - staMenit;

        if (diff > 0) {
            delayValue.textContent = diff;
            delayPreview.style.display = 'block';
        } else {
            delayPreview.style.display = 'none';
        }
    }
    // Auto-fill STA & STD saat pilih nomor penerbangan
    $('#flight_schedule_id').on('change', function () {
        const selected = $(this).find(':selected');
        const sta = selected.data('sta');
        const std = selected.data('std');

        if (sta) $('[name="sta"]').val(sta);
        if (std) $('[name="std"]').val(std);

        // Trigger hitung delay jika ATA sudah terisi
        $('[name="ata"]').trigger('change');
    });

    ataInput.addEventListener('change', hitungDelay);
    staInput.addEventListener('change', hitungDelay);
</script>
@endpush