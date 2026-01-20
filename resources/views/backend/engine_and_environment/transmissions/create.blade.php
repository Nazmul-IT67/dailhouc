@extends('backend.app')

@section('title', 'Add Transmission')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-cogs fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Add Transmission</h2>
                        </div>
                        <a href="{{ route('admin.transmissions.index') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card p-4">
                        <form action="{{ route('admin.transmissions.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Transmission Title (En)</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" placeholder="Enter transmission title" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Transmission Title (Fr)</label>
                                <input type="text" class="form-control" id="title_fr" name="title_fr"
                                    value="{{ old('title_fr') }}" placeholder="Enter transmission title" required>
                                @error('title_fr')
                                    <span class="text-danger">{{ $message }}</span>
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
