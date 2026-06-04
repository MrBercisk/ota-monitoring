@extends('layouts.app')
@section('title', 'Master User')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">👤 Manajemen User</h5>
                <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri ri-add-line me-1"></i> Tambah User
                </a>
            </div>
            <div class="card-body">
                <x-datatable
                    table-id="userTable"
                    ajax-url="{{ route('user.datatable') }}"
                    :columns="[
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'label' => '#',      'orderable' => false, 'searchable' => false, 'width' => '5%'],
                        ['data' => 'name',         'name' => 'name',         'label' => 'Nama'],
                        ['data' => 'email',        'name' => 'email',        'label' => 'Email'],
                        ['data' => 'role_badge',   'name' => 'role',         'label' => 'Role'],
                        ['data' => 'created_fmt',  'name' => 'created_at',   'label' => 'Dibuat',  'searchable' => false],
                        ['data' => 'aksi',         'name' => 'aksi',         'label' => 'Aksi',    'orderable' => false, 'searchable' => false, 'width' => '10%'],
                    ]"
                    :order="[[1, 'asc']]"
                    :page-length="25"
                />
            </div>
        </div>
    </div>
</div>
@endsection