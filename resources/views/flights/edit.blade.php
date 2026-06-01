@extends('layouts.app')

@section('title', 'Edit Data Penerbangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✏️ Edit Data Penerbangan</h5>
                <a href="{{ route('flights.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('flights.update', $flight) }}">
                    @csrf @method('PUT')

                    <div class="row g-3">

                        {{-- Tanggal & Flight Number --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penerbangan <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="flight_date"
                                   class="form-control @error('flight_date') is-invalid @enderror"
                                   value="{{ old('flight_date', $flight->flight_date->format('Y-m-d')) }}">
                            @error('flight_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Penerbangan <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="flight_number"
                                   class="form-control @error('flight_number') is-invalid @enderror"
                                   value="{{ old('flight_number', $flight->flight_number) }}"
                                   placeholder="cth: GA-101"
                                   style="text-transform: uppercase">
                            @error('flight_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Station --}}
                        <div class="col-md-6">
                            <label class="form-label">Station <span class="text-danger">*</span></label>
                            <select name="station_id"
                                    class="form-select select2-placeholder @error('station_id') is-invalid @enderror">
                                <option value="">-- Pilih Station --</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}"
                                        {{ old('station_id', $flight->station_id) == $station->id ? 'selected' : '' }}>
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
                            <select name="delay_code" class="form-select select2-placeholder @error('delay_code') is-invalid @enderror">
                                <option value="">-- Pilih Delay Code --</option>
                                @php $currentCategory = '' @endphp
                                @foreach($delayCodes as $dc)
                                    @if($dc->category !== $currentCategory)
                                        @if($currentCategory !== '') </optgroup> @endif
                                        <optgroup label="{{ $dc->category }}">
                                        @php $currentCategory = $dc->category @endphp
                                    @endif
                                    <option value="{{ $dc->code }}"
                                        {{ old('delay_code', $flight->delay_code) == $dc->code ? 'selected' : '' }}>
                                        {{ $dc->code }} — {{ $dc->reason }}
                                    </option>
                                @endforeach
                                </optgroup>
                            </select>
                            @error('delay_code')
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
                                   value="{{ old('sta', $flight->sta) }}"
                                   id="sta">
                            @error('sta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">STD (Scheduled Time Departure) <span class="text-danger">*</span></label>
                            <input type="time"
                                   name="std"
                                   class="form-control @error('std') is-invalid @enderror"
                                   value="{{ old('std', $flight->std) }}">
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
                                   value="{{ old('ata', $flight->ata) }}"
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
                                   value="{{ old('atd', $flight->atd) }}">
                            @error('atd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview Delay --}}
                        <div class="col-12" id="delay-preview" style="{{ $flight->delay_minutes > 0 ? '' : 'display: none;' }}">
                            <div class="alert alert-warning mb-0 py-2">
                                <i class="ri ri-time-line me-1"></i>
                                Estimasi delay: <strong id="delay-value">{{ $flight->delay_minutes }}</strong> menit
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
                            <select name="remarks" class="form-select @error('remarks') is-invalid @enderror">
                                <option value="">-- Pilih Remarks --</option>
                                <option value="night_stop" {{ old('remarks', $flight->remarks) == 'night_stop' ? 'selected' : '' }}>Night Stop</option>
                                <option value="cancel"     {{ old('remarks', $flight->remarks) == 'cancel'     ? 'selected' : '' }}>Cancel</option>
                                <option value="divert"     {{ old('remarks', $flight->remarks) == 'divert'     ? 'selected' : '' }}>Divert</option>
                            </select>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info Status Saat Ini --}}
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="w-100 p-2 bg-light rounded text-center">
                                <small class="text-muted d-block mb-1">Status saat ini</small>
                                @if($flight->status === 'on_time')
                                    <span class="badge bg-success">ON TIME</span>
                                @elseif($flight->status === 'delayed')
                                    <span class="badge bg-danger">DELAYED — {{ $flight->delay_minutes }} mnt</span>
                                @else
                                    <span class="badge bg-secondary">NIGHT STOP</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Update
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
    const staInput = document.getElementById('sta');
    const ataInput = document.getElementById('ata');
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
        const diff = (ataH * 60 + ataM) - (staH * 60 + staM);

        if (diff > 0) {
            delayValue.textContent = diff;
            delayPreview.style.display = 'block';
        } else {
            delayPreview.style.display = 'none';
        }
    }

    ataInput.addEventListener('change', hitungDelay);
    staInput.addEventListener('change', hitungDelay);
</script>
@endpush