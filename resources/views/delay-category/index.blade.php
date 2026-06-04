@extends('layouts.app')
@section('title', 'Master Delay Category')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Delay Category</h5>
                <a href="{{ route('delay-category.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Tambah Delay Category
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="delayCategoryTable"
                    ajax-url="{{ route('delay-category.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex',   'name' => 'DT_RowIndex', 'label' => '#',            'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'name',           'name' => 'name',        'label' => 'Nama Kategori'],
                        ['data' => 'delay_codes_count', 'name' => 'delay_codes_count', 'label' => 'Jumlah Kode', 'searchable' => false, 'width' => '15%'],
                        ['data' => 'aksi',           'name' => 'aksi',        'label' => 'Aksi',         'orderable' => false, 'searchable' => false, 'width' => '15%'],
                    ]"
                    :order="[[1, 'asc']]"
                    :page-length="25"
                />
            </div>
        </div>
    </div>
</div>
@endsection