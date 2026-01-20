@extends('backend.app')

@section('title', 'Vehicles')

@section('content')
    <div class="page-body">
        <div class="container-fluid">

            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-car fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Vehicles</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DataTable -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">
                        <table id="vehicles-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>User</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Featured Request</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
            $('#vehicles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.vehicles.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'brand',
                        name: 'brand'
                    },
                    {
                        data: 'model',
                        name: 'model'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'featured',
                        name: 'featured',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'featured_request',
                        name: 'featured_request',
                        orderable: false,
                        searchable: false
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

        function deleteVehicle(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will be deleted permanently!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('admin.vehicles.destroy', ':id') }}';
                    let csrfToken = '{{ csrf_token() }}';
                    $.ajax({
                        type: "DELETE",
                        url: url.replace(':id', id),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(resp) {
                            $('#vehicles-table').DataTable().ajax.reload();
                            Swal.fire(resp.success ? 'Deleted!' : 'Error!', resp.message, resp.success ?
                                'success' : 'error');
                        }
                    });
                }
            });
        }

        $(document).on('change', '.toggle-status', function() {
            let vehicleId = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '/admin/vehicles/update-status/' + vehicleId, // make this route
                type: 'PATCH',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('change', '.toggle-feature', function() {
            let vehicleId = $(this).data('id');
            let is_featured = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '/admin/vehicles/update-feature/' + vehicleId, // নতুন route
                type: 'PATCH',
                data: {
                    is_featured: is_featured,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#vehicles-table').DataTable().ajax.reload();
                }
            });
        });
    </script>
@endpush
