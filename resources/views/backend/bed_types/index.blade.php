@extends('backend.app')

@section('title', 'Bed Types')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- Header with title and add button -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-bed fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Bed Types</h2>
                        </div>
                        <a href="{{ route('admin.bed_types.create') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-plus"></i> Add Bed Type
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">
                        <table id="bed-type-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name (En)</th>
                                    <th>Name (Fr)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#bed-type-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.bed_types.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'name_fr',
                        name: 'name_fr'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will be deleted permanently!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) deleteItem(id);
            });
        }

        function deleteItem(id) {
            let url = '{{ route('admin.bed_types.destroy', ':id') }}';
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#bed-type-table').DataTable().ajax.reload();
                    Swal.fire(resp.success ? 'Deleted!' : 'Error!', resp.message, resp.success ? 'success' :
                        'error');
                }
            });
        }
    </script>
@endpush
