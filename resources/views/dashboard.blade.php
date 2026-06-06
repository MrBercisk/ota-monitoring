@extends('layouts.app')

@section('title', 'Dashboard OTA')

@section('content')

{{-- Row 1: 4 Stat Cards --}}
<div class="row g-3 mb-3">
  <div class="col-lg-3 col-md-6">
    <div class="card border-0 h-100">
      <div class="card-body">
        <div class="avatar bg-label-primary rounded mb-2 p-2 d-inline-flex">
          <i class="menu-icon tf-icons ri ri-shield-check-line"></i>
        </div>
        <p class="text-muted small mb-1">OTA Bulan Ini</p>
        <h3 class="fw-bold text-primary mb-0">{{ $otaPercentage }}%</h3>
        <small class="text-success"><i class="ri ri-arrow-up-line"></i> +2.3% dari bulan lalu</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card border-0 h-100">
      <div class="card-body">
        <div class="avatar bg-label-success rounded mb-2 p-2 d-inline-flex">
          <i class="menu-icon tf-icons ri ri-plane-line"></i>
        </div>
        <p class="text-muted small mb-1">Flight Hari Ini</p>
        <h3 class="fw-bold mb-0">{{ $todayFlights }}</h3>
        <small class="text-muted">{{ $todayOnTime }} on time · {{ $todayDelayed }} delayed</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card border-0 h-100">
      <div class="card-body">
        <div class="avatar bg-label-danger rounded mb-2 p-2 d-inline-flex">
          <i class="menu-icon tf-icons ri ri-error-warning-line"></i>
        </div>
        <p class="text-muted small mb-1">Total Delayed</p>
        <h3 class="fw-bold text-danger mb-0">{{ $delayed }}</h3>
        <small class="text-muted">Flight terlambat bulan ini</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card border-0 h-100">
      <div class="card-body">
        <div class="avatar bg-label-warning rounded mb-2 p-2 d-inline-flex">
          <i class="menu-icon tf-icons ri ri-time-line"></i>
        </div>
        <p class="text-muted small mb-1">Total Delay Minutes</p>
        <h3 class="fw-bold text-warning mb-0">{{ number_format($totalDelayMinutes) }}</h3>
        <small class="text-muted">menit akumulasi delay</small>
      </div>
    </div>
  </div>
</div>

{{-- Row 2: Grafik OTA Trend --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bx bx-line-chart me-1"></i> Grafik OTA Trend — 30 Hari Terakhir</h6>
        <div class="d-flex gap-3">
          <small><span class="badge bg-primary">&nbsp;</span> OTA %</small>
          <small><span class="badge bg-danger">&nbsp;</span> Delay count</small>
        </div>
      </div>
      <div class="card-body">
        <canvas id="otaTrendChart" height="80"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- Row 3: Top Delay Station + Top Delay Code --}}
<div class="row g-3 mb-3">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bx bx-map-pin me-1"></i> Top Delay Station</h6>
        <a href="{{ route('reports.index', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date'   => now()->toDateString(),
        ]) }}" class="text-muted small">Detail →</a>
      </div>
      <div class="card-body p-0">
        @foreach($topDelayStations as $i => $s)
        <div class="d-flex align-items-center px-3 py-2 border-bottom">
          <span class="text-muted me-2" style="width:20px;font-size:12px;">{{ $i+1 }}</span>
          <div class="flex-grow-1">
            <strong>{{ $s['code'] }}</strong>
            <small class="text-muted"> — {{ $s['name'] }}</small>
            <div class="progress mt-1" style="height:3px;">
              <div class="progress-bar bg-danger" style="width:{{ $s['pct_of_max'] }}%"></div>
            </div>
          </div>
          <span class="ms-3 text-danger fw-bold small">{{ $s['delayed'] }} flight</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bx bx-tag me-1"></i> Top Delay Code</h6>
        <a href="{{ route('delay.index') }}" class="text-muted small">Detail →</a>
      </div>
      <div class="card-body p-0">
        @foreach($topDelayCodes as $i => $c)
        <div class="d-flex align-items-center px-3 py-2 border-bottom">
          <span class="text-muted me-2" style="width:20px;font-size:12px;">{{ $i+1 }}</span>
          <div class="flex-grow-1">
            <strong>{{ $c['code'] }}</strong>
            <small class="text-muted"> — {{ $c['label'] }}</small>
            <div class="progress mt-1" style="height:3px;">
              <div class="progress-bar bg-warning" style="width:{{ $c['pct_of_max'] }}%"></div>
            </div>
          </div>
          <span class="ms-3 text-warning fw-bold small">{{ number_format($c['total_minutes']) }} mnt</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Row 4: Tabel Rekap OTA Per Station --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bx bx-table me-1"></i> Rekap OTA Per Station — Bulan Ini</h6>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-primary">
          <i class="bx bx-file me-1"></i> Buat Laporan
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
          <thead class="table-dark">
            <tr>
              <th>Station</th>
              <th class="text-center">Total</th>
              <th class="text-center">On Time</th>
              <th class="text-center">Delayed</th>
              <th class="text-center">Night Stop</th>
              <th class="text-end" style="min-width:130px;">OTA %</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stationStats as $stat)
            @php $pct = $stat['percentage']; @endphp
            <tr>
              <td><strong>{{ $stat['code'] }}</strong> <small class="text-muted">— {{ $stat['name'] }}</small></td>
              <td class="text-center">{{ $stat['total'] }}</td>
              <td class="text-center"><span class="badge bg-success">{{ $stat['on_time'] }}</span></td>
              <td class="text-center"><span class="badge bg-danger">{{ $stat['delayed'] }}</span></td>
              <td class="text-center"><span class="badge bg-secondary">{{ $stat['night_stop'] }}</span></td>
              <td class="text-end">
                <div class="d-flex align-items-center justify-content-end gap-2">
                  <div class="progress flex-grow-1" style="height:4px;max-width:80px;">
                    <div class="progress-bar {{ $pct == 100 ? 'bg-success' : ($pct >= 80 ? 'bg-warning' : 'bg-danger') }}"
                         style="width:{{ $pct }}%"></div>
                  </div>
                  <span class="badge {{ $pct == 100 ? 'bg-success' : ($pct >= 80 ? 'bg-warning' : 'bg-danger') }}">
                    {{ $pct }}%
                  </span>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data bulan ini</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    window.DASHBOARD = {
        trendLabels: @json($trendLabels),
        trendOta   : @json($trendOta),
        trendDelay : @json($trendDelay),
    };
</script>
<script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
@endpush
@endsection