@extends('backend.app')

@section('title', 'Edit Bed Type')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card p-4">
                        <h4>Edit Bed Type</h4>
                        <form action="{{ route('admin.bed_types.update', $bedType->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Bed Type Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $bedType->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Condition Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror"
                                    value="{{ old('name_fr', $bedType->translations->where('language', 'fr')->first()->name ?? '') }}" 
                                    placeholder="Enter condition name" required>
                                
                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('admin.bed_types.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
