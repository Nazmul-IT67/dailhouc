@extends('backend.app')

@section('title', 'Edit Body Color')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card p-4">
                        <h4 class="mb-4">Edit Body Color</h4>

                        <form action="{{ route('admin.body_colors.update', $color->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Color Name (En)<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $color->name) }}" placeholder="Enter color name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Condition Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror"
                                    value="{{ old('name_fr', $color->translations->where('language', 'fr')->first()->name ?? '') }}" 
                                    placeholder="Enter condition name" required>
                                
                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="color_code" class="form-label">Color Code (HEX)</label>
                                <input type="color" name="color_code" id="color_code"
                                    class="form-control @error('color_code') is-invalid @enderror"
                                    value="{{ old('color_code', $color->color_code) }}" placeholder="#FFFFFF">
                                @error('color_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.body_colors.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Body Color</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
