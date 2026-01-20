@extends('backend.app')

@section('title', 'Vehicle Conditions')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-paint-brush fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Vehicle Condition</h2>
                        </div>
                        <a href="{{ route('admin.interior_colors.create') }}" class="btn  btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-plus"></i> Add Vehicle Condition
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">

                        <div class="mb-3 text-end">
                            <a href="{{ route('admin.vehicle_conditions.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Vehicle Condition
                            </a>
                        </div>

                        <table id="vehicle-condition-table" class="table table-bordered">
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
            $('#vehicle-condition-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.vehicle_conditions.index') }}",
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
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        function deleteItem(id) {
            let url = '{{ route('admin.vehicle_conditions.destroy', ':id') }}';
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#vehicle-condition-table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        Swal.fire('Deleted!', resp.message, 'success');
                    } else if (resp.errors) {
                        Swal.fire('Error!', resp.errors[0], 'error');
                    } else {
                        Swal.fire('Error!', resp.message, 'error');
                    }
                }
            });
        }
    </script>
@endpush
