@extends('backend.app')

@section('title', 'Edit Equipment')

@section('content')
    <div class="page-body">
        <div class="container-fluid">

            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white;">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-cogs fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Edit Equipment</h2>
                        </div>
                        <a href="{{ route('admin.equipment.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card p-4">
                        <form action="{{ route('admin.equipment.update', $equipment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Equipment Title (En)</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $equipment->title) }}" required>
                                @error('title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Equipment Title (Fr) </label>
                                <input type="text" name="title_fr" id="title_fr"
                                    class="form-control @error('title_fr') is-invalid @enderror"
                                    value="{{ old('title_fr', $equipment->translations->where('language', 'fr')->first()->title ?? '') }}" 
                                    placeholder="Enter title in French" required>
                                
                                @error('title_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Equipment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
