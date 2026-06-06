@extends('layouts.app')
@section('title', 'Master Delay Code')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Delay Code</h5>
                <a href="{{ route('delay.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Tambah Delay Code
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="delayTable"
                    ajax-url="{{ route('delay.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex',    'name' => 'DT_RowIndex', 'label' => '#',        'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'code_badge',     'name' => 'code',        'label' => 'Kode'],
                        ['data' => 'reason',         'name' => 'reason',      'label' => 'Reason'],
                        ['data' => 'category_name',  'name' => 'category',    'label' => 'Kategori', 'searchable' => false],
                        ['data' => 'aksi',           'name' => 'aksi',        'label' => 'Aksi',     'orderable' => false, 'searchable' => false, 'width' => '15%'],
                    ]"
                    :order="[[1, 'asc']]"
                    :page-length="25"
                />
            </div>
        </div>
    </div>
</div>
@endsection