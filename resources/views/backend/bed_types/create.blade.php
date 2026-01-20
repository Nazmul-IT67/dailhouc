@extends('backend.app')

@section('title', 'Add Bed Type')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card p-4">
                        <h4>Add Bed Type</h4>
                        <form action="{{ route('admin.bed_types.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Bed Type Name (En)</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Bed Type Name (Fr)</label>
                                <input type="text" class="form-control" id="name_fr" name="name_fr"
                                    value="{{ old('name_fr') }}" required>
                            </div>
                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="{{ route('admin.bed_types.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
