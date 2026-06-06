@extends('layouts.app')

@section('title', 'Reports OTA')

@section('content')

{{-- Filter Bar --}}
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Tanggal Mulai</label>
        <input type="date" name="start_date" class="form-control"
               value="{{ $startDate->toDateString() }}"
               max="{{ now()->toDateString() }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Tanggal Akhir</label>
        <input type="date" name="end_date" class="form-control"
               value="{{ $endDate->toDateString() }}"
               max="{{ now()->toDateString() }}">
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-primary">
          <i class="menu-icon tf-icons ri ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
      </div>
      <div class="col-md-auto ms-md-auto">
        <a href="{{ route('reports.export', request()->only('start_date','end_date')) }}"
           class="btn btn-outline-success">
          <i class="menu-icon tf-icons ri ri-file-excel-2-line"></i> Export
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Grouped Bar Chart --}}
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="mb-0">
      <i class="menu-icon tf-icons ri ri-bar-chart-grouped-line"></i>
      OTA Recap Per Station
      <small class="text-muted fw-normal ms-1">
        {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
      </small>
    </h6>
  </div>
  <div class="card-body">
    @if($activeStations->isEmpty())
      <div class="text-center text-muted py-5">
        <i class="ri ri-inbox-line" style="font-size:2rem;"></i>
        <p class="mt-2">Tidak ada data pada rentang tanggal ini.</p>
      </div>
    @else
    <div style="position:relative; width:100%; height:{{ max(350, $activeStations->count() * 35 + 100) }}px; overflow-x:auto;">
        <canvas id="otaGroupedChart"
                style="min-width:{{ max(700, $activeStations->count() * 80) }}px;"
                role="img"
                aria-label="Grouped bar chart OTA per station per hari">
            Data OTA per station dan tanggal
        </canvas>
    </div>
    @endif
  </div>
</div>

{{-- Tabel Rekap --}}
<div class="card">
  <div class="card-header">
    <h6 class="mb-0">
      <i class="menu-icon tf-icons ri ri-table-line"></i> Tabel Rekap OTA Per Station
    </h6>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm mb-0 align-middle">
      <thead class="table-dark">
        <tr>
          <th style="min-width:130px;">Station</th>
          <th class="text-center">Total</th>
          <th class="text-center">On Time</th>
          <th class="text-center">Delayed</th>
          <th class="text-center" style="min-width:110px;">OTA %</th>
          @foreach($dates as $date)
            <th class="text-center" style="min-width:54px; font-size:11px;">
              {{ \Carbon\Carbon::parse($date)->format('d/m') }}
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($tableData as $row)
        @php $pct = $row['pct']; @endphp
        <tr>
          <td>
            <strong>{{ $row['code'] }}</strong>
            <small class="text-muted d-block" style="font-size:11px;">{{ $row['name'] }}</small>
          </td>
          <td class="text-center">{{ $row['total'] }}</td>
          <td class="text-center"><span class="badge bg-success">{{ $row['on_time'] }}</span></td>
          <td class="text-center"><span class="badge bg-danger">{{ $row['delayed'] }}</span></td>
          <td class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
              <div class="progress flex-grow-1" style="height:4px; max-width:60px;">
                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : ($pct >= 80 ? 'bg-warning' : 'bg-danger') }}"
                     style="width:{{ $pct }}%"></div>
              </div>
              <span class="badge {{ $pct == 100 ? 'bg-success' : ($pct >= 80 ? 'bg-warning' : 'bg-danger') }}">
                {{ $pct }}%
              </span>
            </div>
          </td>
          @foreach($row['daily_pct'] as $dayPct)
          <td class="text-center">
            @if($dayPct === null)
              <span class="text-muted" style="font-size:11px;">—</span>
            @else
              <span class="badge {{ $dayPct == 100 ? 'bg-label-success' : ($dayPct >= 80 ? 'bg-label-warning' : 'bg-label-danger') }}"
                    style="font-size:11px;">
                {{ $dayPct }}%
              </span>
            @endif
          </td>
          @endforeach
        </tr>
        @empty
        <tr>
          <td colspan="{{ 5 + $dates->count() }}" class="text-center text-muted py-4">
            Tidak ada data pada rentang tanggal ini.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
    window.REPORTS = {
        stationLabels: @json($activeStations->pluck('code')->values()),
        rawDatasets  : @json($chartDatasets),
        dateLabels   : @json($dates->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))),
    };
</script>
<script src="{{ asset('assets/js/pages/reports.js') }}"></script>
@endpush