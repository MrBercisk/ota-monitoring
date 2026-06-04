@extends('layouts.app')
@section('title', 'Master Station')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🗺️ Master Station</h5>
                <a href="{{ route('stations.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Tambah Station
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="stationTable"
                    ajax-url="{{ route('stations.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex',    'name' => 'DT_RowIndex', 'label' => '#',                  'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'code_badge',     'name' => 'code',        'label' => 'Kode'],
                        ['data' => 'name',           'name' => 'name',        'label' => 'Nama Station'],
                        ['data' => 'flights_badge',  'name' => 'flights_count','label' => 'Jumlah Penerbangan', 'searchable' => false],
                        ['data' => 'aksi',           'name' => 'aksi',        'label' => 'Aksi',               'orderable' => false, 'searchable' => false, 'width' => '10%'],
                    ]"
                    :order="[[1, 'asc']]"
                    :page-length="25"
                />
            </div>
        </div>
    </div>
</div>
@endsection