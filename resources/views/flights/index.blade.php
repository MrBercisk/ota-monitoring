@extends('layouts.app')

@section('title', 'Data Penerbangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✈️ Data Penerbangan</h5>
                <a href="{{ route('flights.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Input Data Baru
                </a>
            </div>

            {{-- Filter --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Station</label>
                        <select name="station_id" class="form-select select2-placeholder  form-select-sm">
                            <option value="">Semua Station</option>
                            @foreach($stations as $station)
                                <option value="{{ $station->id }}"
                                    {{ request('station_id') == $station->id ? 'selected' : '' }}>
                                    {{ $station->code }} - {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bx bx-filter-alt"></i> Filter
                        </button>
                        <a href="{{ route('flights.index') }}" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <strong>📊 Export Laporan OTA</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
            
                        {{-- Export Weekly --}}
                        <div class="col-md-6">
                            <form action="{{ route('flights.export.weekly') }}" method="GET">
                                <label class="form-label fw-bold">Export Weekly</label>
                                <div class="input-group">
                                    <input type="date"
                                        name="week_date"
                                        class="form-control"
                                        value="{{ date('Y-m-d') }}"
                                        required>
            
                                    {{-- Optional: filter station --}}
                                    <select name="station_id" class="form-select">
                                        <option value="">Semua Station</option>
                                        @foreach($stations as $station)
                                            <option value="{{ $station->id }}">{{ $station->code }}</option>
                                        @endforeach
                                    </select>
            
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-file-earmark-excel"></i> Download
                                    </button>
                                </div>
                                <small class="text-muted">Pilih tanggal mana saja dalam minggu yang diinginkan</small>
                            </form>
                        </div>
            
                        {{-- Export Monthly --}}
                        <div class="col-md-6">
                            <form action="{{ route('flights.export.monthly') }}" method="GET">
                                <label class="form-label fw-bold">Export Monthly</label>
                                <div class="input-group">
                                    <input type="month"
                                        name="month"
                                        class="form-control"
                                        value="{{ date('Y-m') }}"
                                        required>
            
                                    {{-- Optional: filter station --}}
                                    <select name="station_id" class="form-select">
                                        <option value="">Semua Station</option>
                                        @foreach($stations as $station)
                                            <option value="{{ $station->id }}">{{ $station->code }}</option>
                                        @endforeach
                                    </select>
            
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-file-earmark-excel"></i> Download
                                    </button>
                                </div>
                                <small class="text-muted">Pilih bulan yang diinginkan</small>
                            </form>
                        </div>
            
                    </div>
                </div>
            </div>


            {{-- Tabel --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Flight</th>
                            <th>Station</th>
                            <th>STA</th>
                            <th>STD</th>
                            <th>ATA</th>
                            <th>ATD</th>
                            <th>Delay</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flights as $flight)
                        <tr class="{{ $flight->status === 'delayed' ? 'table-danger' : '' }}">
                            <td>{{ $flight->flight_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $flight->flight_number }}</strong></td>
                            <td>{{ $flight->station->code }}</td>
                            <td>{{ $flight->sta }}</td>
                            <td>{{ $flight->std }}</td>
                            <td>{{ $flight->ata ?? '-' }}</td>
                            <td>{{ $flight->atd ?? '-' }}</td>
                            <td>
                                @if($flight->delay_minutes > 0)
                                    <span class="badge bg-danger">{{ $flight->delay_minutes }} mnt</span>
                                @else
                                    <span class="badge bg-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($flight->status === 'on_time')
                                    <span class="badge bg-success">ON TIME</span>
                                @elseif($flight->status === 'delayed')
                                    <span class="badge bg-danger">DELAYED</span>
                                @else
                                    <span class="badge bg-secondary">NIGHT STOP</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('flights.edit', $flight) }}"
                                   class="btn btn-sm btn-icon btn-outline-primary">
                                    <i class="ri ri-edit-line"></i>
                                </a>
                                <form action="{{ route('flights.destroy', $flight) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-icon btn-outline-danger">
                                        <i class="ri ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Belum ada data penerbangan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $flights->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection