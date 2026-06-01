@extends('layouts.app')

@section('title', 'Dashboard OTA')

@section('content')

<div class="row">
    <!-- Header -->
    <div class="col-12 mb-4">
        <h4 class="fw-bold">Dashboard OTA Monitoring</h4>
        <p class="text-muted">Ringkasan performa ketepatan waktu penerbangan</p>
    </div>

    <!-- Card Stats -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Penerbangan</p>
                        <h4 class="fw-bold mb-0">{{ $totalFlights }}</h4>
                    </div>
                    <div class="avatar bg-label-primary p-2 rounded">
                        <i class="bx bx-plane bx-md"></i>
                    </div>
                </div>
                <small class="text-muted">Bulan ini</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">On Time</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $onTime }}</h4>
                    </div>
                    <div class="avatar bg-label-success p-2 rounded">
                        <i class="bx bx-check-circle bx-md"></i>
                    </div>
                </div>
                <small class="text-success">{{ $otaPercentage }}% OTA</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Delayed</p>
                        <h4 class="fw-bold mb-0 text-danger">{{ $delayed }}</h4>
                    </div>
                    <div class="avatar bg-label-danger p-2 rounded">
                        <i class="bx bx-time bx-md"></i>
                    </div>
                </div>
                <small class="text-muted">Flight terlambat</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Station</p>
                        <h4 class="fw-bold mb-0">{{ $totalStations }}</h4>
                    </div>
                    <div class="avatar bg-label-info p-2 rounded">
                        <i class="bx bx-map bx-md"></i>
                    </div>
                </div>
                <small class="text-muted">Station aktif</small>
            </div>
        </div>
    </div>
</div>

<!-- Rekap OTA Per Station -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📊 Rekap OTA Per Station — Bulan Ini</h5>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-file me-1"></i> Buat Laporan
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Station</th>
                            <th class="text-center">Total Flight</th>
                            <th class="text-center">On Time</th>
                            <th class="text-center">Delayed</th>
                            <th class="text-center">Night Stop</th>
                            <th class="text-center">OTA %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stationStats as $stat)
                        <tr>
                            <td><strong>{{ $stat['code'] }}</strong> — {{ $stat['name'] }}</td>
                            <td class="text-center">{{ $stat['total'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $stat['on_time'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $stat['delayed'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $stat['night_stop'] }}</span>
                            </td>
                            <td class="text-center">
                                @php $pct = $stat['percentage'] @endphp
                                <span class="badge {{ $pct == 100 ? 'bg-success' : ($pct >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data bulan ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection