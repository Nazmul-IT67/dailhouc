@extends('backend.app')

@section('title', 'Add Body Type')

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
                            <h2 class="mb-0 text-white">Add Body Type</h2>
                        </div>
                        <a href="{{ route('admin.body_types.index') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card p-4">
                        <form action="{{ route('admin.body_types.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Body Type title (En)</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" placeholder="Enter body type title" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Body Type title (Fr)</label>
                                <input type="text" class="form-control" id="title_fr" name="title_fr"
                                    value="{{ old('title_fr') }}" placeholder="Enter body type title" required>
                                @error('title_fr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold">Select Category</label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Icon Upload -->
                            <div class="mb-3">
                                <label for="icon" class="form-label fw-semibold">Icon (optional)</label>
                                <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                                @error('icon')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
