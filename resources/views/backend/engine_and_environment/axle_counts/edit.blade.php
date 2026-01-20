@extends('backend.app')

@section('title', 'Edit Axle Count')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-pencil fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Edit Axle Count</h2>
                        </div>
                        <a href="{{ route('admin.axle-counts.index') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="p-4 card">
                        <form action="{{ route('admin.axle-counts.update', $axleCount->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="count" class="form-label">Axle Count</label>
                                <input type="number" name="count" id="count"
                                    class="form-control @error('count') is-invalid @enderror"
                                    value="{{ old('count', $axleCount->count) }}" min="1" required>
                                @error('count')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Axle Count</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
