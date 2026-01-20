@extends('backend.app')

@section('title', 'Sub Models')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">

                        <div class="mb-3 text-end">
                            <a href="{{ route('admin.sub_models.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Sub Model
                            </a>
                        </div>

                        <table id="sub-model-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sub Model Name (En)</th>
                                    <th>Sub Model Name (Fr)</th>
                                    <th>Car Model</th>
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
            $('#sub-model-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.sub_models.index') }}",
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
                        data: 'car_model',
                        name: 'car_model'
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
                title: 'Are you sure you want to delete this Sub Model?',
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
            let url = '{{ route('admin.sub_models.destroy', ':id') }}';
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#sub-model-table').DataTable().ajax.reload();
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
