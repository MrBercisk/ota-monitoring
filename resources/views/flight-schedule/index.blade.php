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
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nomor Penerbangan</th>
                            <th>STA</th>
                            <th>STD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-label-primary fs-6">
                                    {{ $schedule->flight_number }}
                                </span>
                            </td>
                            <td>{{ $schedule->sta }}</td>
                            <td>{{ $schedule->std }}</td>
                            <td>
                                <a href="{{ route('flight-schedule.edit', $schedule) }}"
                                   class="btn btn-sm btn-icon btn-outline-primary">
                                    <i class="ri ri-edit-line"></i>
                                </a>
                                <form action="{{ route('flight-schedule.destroy', $schedule) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-outline-danger btn-delete"
                                            data-message="Jadwal {{ $schedule->flight_number }} akan dihapus permanen!">
                                        <i class="ri ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada data jadwal penerbangan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Total {{ $schedules->count() }} jadwal penerbangan
            </div>
        </div>
    </div>
</div>
@endsection