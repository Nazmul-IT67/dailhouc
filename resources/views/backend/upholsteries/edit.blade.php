@extends('backend.app')

@section('title', 'Edit Upholstery')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card p-4">
                        <h4 class="mb-4">Edit Upholstery</h4>

                        <form action="{{ route('admin.upholsteries.update', $upholstery->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Upholstery Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $upholstery->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Condition Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror"
                                    value="{{ old('name_fr', $upholstery->translations->where('language', 'fr')->first()->name ?? '') }}" 
                                    placeholder="Enter condition name" required>
                                
                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.upholsteries.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Upholstery</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
