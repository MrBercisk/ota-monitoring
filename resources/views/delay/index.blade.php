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

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Reason</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delay_codes as $delay)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-label-primary fs-6">
                                    {{ $delay->code }}
                                </span>
                            </td>
                            <td>{{ $delay->reason }}</td>
                            <td>
                                <span class="badge bg-label-info rounded-pill">
                                    {{ $delay->category->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('delay.edit', $delay) }}"
                                   class="btn btn-sm btn-icon btn-outline-primary">
                                    <i class="ri ri-edit-line"></i>
                                </a>
                                <form action="{{ route('delay.destroy', $delay) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                            data-message="Delay Code {{ $delay->code }} akan dihapus permanen!">
                                        <i class="ri ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data station
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-muted">
                Total {{ $delay_codes->count() }} station
            </div>
        </div>
    </div>
</div>
@endsection