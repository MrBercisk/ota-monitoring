@extends('layouts.app')
@section('title', 'Data Penerbangan')
@section('content')
<div class="row">
    <div class="col-12">

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="ri ri-filter-line me-1"></i> Filter Data</h6>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Station</label>
                        <select name="station_id" id="filter_station" class="form-select form-select-sm select2-placeholder">
                            <option value="">Semua Station</option>
                            @foreach($stations as $station)
                                <option value="{{ $station->id }}">
                                    {{ $station->code }} - {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" id="filter_date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" id="filter_date_to" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="button" id="btnFilter" class="btn btn-primary btn-sm">
                            <i class="ri ri-filter-line me-1"></i> Filter
                        </button>
                        <button type="button" id="btnReset" class="btn btn-outline-secondary btn-sm">
                            <i class="ri ri-refresh-line me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Export --}}
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <strong><i class="ri ri-file-excel-2-line me-1"></i> Export Laporan OTA</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <form action="{{ route('flights.export.weekly') }}" method="GET">
                            <label class="form-label fw-bold">Export Weekly</label>
                            <div class="input-group">
                                <input type="date" name="week_date" class="form-control"
                                       value="{{ date('Y-m-d') }}" required>
                                <select name="station_id" class="form-select">
                                    <option value="">Semua Station</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->code }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-success">
                                    <i class="ri ri-download-line"></i> Download
                                </button>
                            </div>
                            <small class="text-muted">Pilih tanggal mana saja dalam minggu yang diinginkan</small>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('flights.export.monthly') }}" method="GET">
                            <label class="form-label fw-bold">Export Monthly</label>
                            <div class="input-group">
                                <input type="month" name="month" class="form-control"
                                       value="{{ date('Y-m') }}" required>
                                <select name="station_id" class="form-select">
                                    <option value="">Semua Station</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->code }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri ri-download-line"></i> Download
                                </button>
                            </div>
                            <small class="text-muted">Pilih bulan yang diinginkan</small>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✈️ Data Penerbangan</h5>
                <a href="{{ route('flights.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Input Data Baru
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="flightsTable"
                    ajax-url="{{ route('flights.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex',    'name' => 'DT_RowIndex',  'label' => '#',       'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'tanggal',        'name' => 'tanggal',       'label' => 'Tanggal'],
                        ['data' => 'flight_number',  'name' => 'flight_number', 'label' => 'Flight'],
                        ['data' => 'station_badge',  'name' => 'station_id',    'label' => 'Station', 'searchable' => false],
                        ['data' => 'sta',            'name' => 'sta',           'label' => 'STA'],
                        ['data' => 'std',            'name' => 'std',           'label' => 'STD'],
                        ['data' => 'ata',            'name' => 'ata',           'label' => 'ATA'],
                        ['data' => 'atd',            'name' => 'atd',           'label' => 'ATD'],
                        ['data' => 'delay_badge',    'name' => 'delay_minutes', 'label' => 'Delay',   'searchable' => false],
                        ['data' => 'status_badge',   'name' => 'status',        'label' => 'Status',  'searchable' => false],
                        ['data' => 'aksi',           'name' => 'aksi',          'label' => 'Aksi',    'orderable' => false, 'searchable' => false, 'width' => '10%'],
                    ]"
                    :order="[[1, 'desc']]"
                    :page-length="25"
                />
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Filter DataTables via AJAX params
let flightsTable = $('#flightsTable').DataTable();

$('#btnFilter').on('click', function () {
    flightsTable.ajax.url(
        '{{ route('flights.datatable') }}' +
        '?station_id=' + $('#filter_station').val() +
        '&date_from='  + $('#filter_date_from').val() +
        '&date_to='    + $('#filter_date_to').val()
    ).load();
});

$('#btnReset').on('click', function () {
    $('#filter_station').val('').trigger('change');
    $('#filter_date_from').val('');
    $('#filter_date_to').val('');
    flightsTable.ajax.url('{{ route('flights.datatable') }}').load();
});
</script>
@endpush
@endsection