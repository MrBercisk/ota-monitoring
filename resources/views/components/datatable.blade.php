{{-- resources/views/components/datatable.blade.php --}}
<table id="{{ $tableId }}" class="table table-hover table-sm w-100">
    <thead class="table-dark">
        <tr>
            @foreach($columns as $col)
                <th {{ isset($col['width']) ? 'style=width:' . $col['width'] : '' }}>
                    {{ $col['label'] }}
                </th>
            @endforeach
        </tr>
    </thead>
</table>

@push('scripts')
@php
    $dtColumns = collect($columns)->map(fn($col) => [
        'data'       => $col['data'],
        'name'       => $col['name']       ?? $col['data'],
        'orderable'  => $col['orderable']  ?? true,
        'searchable' => $col['searchable'] ?? true,
        'width'      => $col['width']      ?? null,
    ])->values()->all();
@endphp
<script>
(function () {
    $('#{{ $tableId }}').DataTable({
        processing: true,
        serverSide: true,
        responsive: {{ $responsive ? 'true' : 'false' }},
        ajax: '{{ $ajaxUrl }}',
        columns: @json($dtColumns),
        order: @json($order),
        pageLength: {{ $pageLength }},
        scrollX: true,  
        language: {
            processing:  '<div class="mt-2 text-primary" role="status"></div> Memuat data',
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            infoFiltered:'(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable:  'Belum ada data',
            paginate: {
                first:    'Pertama',
                last:     'Terakhir',
                next:     'Selanjutnya',
                previous: 'Sebelumnya',
            },
        },
    });

    $(document).on('click', '#{{ $tableId }} .btn-delete', function () {
        const url = $(this).data('url');
        const tableId = '{{ $tableId }}';

        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success(response) {
                    Swal.fire('Terhapus!', response.message, 'success');
                    $('#' + tableId).DataTable().ajax.reload(null, false);
                },
                error(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan.';
                    Swal.fire('Gagal', msg, 'error');
                },
            });
        });
    });
})();
</script>
@endpush