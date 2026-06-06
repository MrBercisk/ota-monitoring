@extends('layouts.app')
@section('title', 'Master Jadwal Penerbangan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">✈️ Master Jadwal Penerbangan</h5>
                <a href="{{ route('flight-schedule.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Tambah Jadwal
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="flightScheduleTable"
                    ajax-url="{{ route('flight-schedule.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex',        'name' => 'DT_RowIndex',    'label' => '#',                  'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'flight_number_badge', 'name' => 'flight_number',  'label' => 'Nomor Penerbangan'],
                        ['data' => 'sta',                 'name' => 'sta',            'label' => 'STA'],
                        ['data' => 'std',                 'name' => 'std',            'label' => 'STD'],
                        ['data' => 'aksi',                'name' => 'aksi',           'label' => 'Aksi', 'orderable' => false, 'searchable' => false, 'width' => '15%'],
                    ]"
                    :order="[[1, 'asc']]"
                    :page-length="25"
                />
            </div>
        </div>
    </div>
</div>
@endsection