@extends('backend.app')

@section('title', 'Edit Cylinder')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-cogs fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Edit Cylinder</h2>
                        </div>
                        <a href="{{ route('admin.cylinders.index') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card p-4">
                        <form action="{{ route('admin.cylinders.update', $cylinder->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="number" class="form-label">Cylinder Number</label>
                                <input type="number" class="form-control" id="number" name="number"
                                    value="{{ old('number', $cylinder->number) }}" required>
                                @error('number')
                                    <span class="text-danger">{{ $message }}</span>
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
