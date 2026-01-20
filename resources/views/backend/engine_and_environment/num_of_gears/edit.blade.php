@extends('backend.app')

@section('title', 'Edit Number of Gear')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-cogs fa-2x me-3"></i>
                            <h2 class="mb-0 text-white">Edit Number of Gear</h2>
                        </div>
                        <a href="{{ route('admin.num_of_gears.index') }}" class="btn btn-lg"
                            style="background-color: white; color: #006666; font-weight: 600; border: 1px solid #ccc;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card p-4">
                        <form action="{{ route('admin.num_of_gears.update', $numOfGear->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="number" class="form-label">Number of Gear</label>
                                <input type="text" class="form-control" id="number" name="number"
                                    value="{{ old('number', $numOfGear->number) }}" required>
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
