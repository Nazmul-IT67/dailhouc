@extends('backend.app')

@section('title', 'Edit Body Type')

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
                            <h2 class="mb-0 text-white">Edit Body Type</h2>
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
                        <form action="{{ route('admin.body_types.update', $bodyType->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Body Type title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title', $bodyType->title) }}" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Body Type title (Fr) </label>
                                <input type="text" name="title_fr" id="title_fr"
                                    class="form-control @error('title_fr') is-invalid @enderror" {{-- .name er bodole .title hobe --}}
                                    value="{{ old('title_fr', $bodyType->translations->where('language', 'fr')->first()->title ?? '') }}"
                                    placeholder="Enter brand name in French" required>

                                @error('title_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold">Select Category</label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $bodyType->category_id) == $category->id ? 'selected' : '' }}>
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

                                @if ($bodyType->icon)
                                    <div class="mt-2">
                                        <p class="mb-1">Current Icon:</p>
                                        <img src="{{ asset($bodyType->icon) }}" alt="Icon"
                                            style="width: 70px; height: 70px; object-fit: contain; border: 1px solid #ccc; border-radius: 8px;">
                                    </div>
                                @endif
                                @error('icon')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
