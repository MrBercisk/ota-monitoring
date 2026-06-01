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

            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Station</th>
                            <th>Jumlah Penerbangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $station)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-label-primary fs-6">
                                    {{ $station->code }}
                                </span>
                            </td>
                            <td>{{ $station->name }}</td>
                            <td>
                                <span class="badge bg-label-info rounded-pill">
                                    {{ $station->flights_count }} penerbangan
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('stations.edit', $station) }}"
                                   class="btn btn-sm btn-icon btn-outline-primary">
                                    <i class="ri ri-edit-line"></i>
                                </a>
                                <form action="{{ route('stations.destroy', $station) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                            data-message="Station {{ $station->code }} akan dihapus permanen!">
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
                Total {{ $stations->count() }} station
            </div>
        </div>
    </div>
</div>
@endsection