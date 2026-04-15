@extends('backend.app')

@section('title', 'Add Equipment')

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
                            <h2 class="mb-0 text-white">Add Equipment</h2>
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
                        <form action="{{ route('admin.equipment.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Equipment Title</label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Enter equipment title" value="{{ old('title') }}" required>
                                @error('title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title_fr" class="form-label fw-bold">Equipment Title (French)</label>
                                <input type="text" name="title_fr" id="title_fr"
                                    class="form-control @error('title_fr') is-invalid @enderror"
                                    placeholder="Entrez le titre de l'équipement" value="{{ old('title_fr') }}" required>
                                @error('title_fr')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Save Equipment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
