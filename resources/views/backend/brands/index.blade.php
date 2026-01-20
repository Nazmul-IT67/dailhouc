@extends('backend.app')

@section('title', 'Brands')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">

                        <div class="mb-3 text-end">
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Brand
                            </a>
                        </div>

                        <table id="brand-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name (En)</th>
                                    <th>Name (Fr)</th>
                                    <th>Category</th>
                                    <th>Logo</th>
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
            $('#brand-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.brands.index') }}",
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
                        data: 'category',
                        name: 'category'
                    }, // new
                    {
                        data: 'logo',
                        name: 'logo',
                        orderable: false,
                        searchable: false
                    }, // new
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
            let url = '{{ route('admin.brands.destroy', ':id') }}';
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#brand-table').DataTable().ajax.reload();
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
