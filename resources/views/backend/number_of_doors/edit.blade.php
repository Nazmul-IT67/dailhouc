@extends('backend.app')

@section('title', 'Edit Number of Door')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card p-4">
                        <h4 class="mb-4">Edit Number of Door</h4>
                        <form action="{{ route('admin.number_of_doors.update', $numberOfDoor->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="number" class="form-label">Number  </label>
                                <input type="number" name="number" id="number"
                                    class="form-control @error('number') is-invalid @enderror"
                                    value="{{ old('number', $numberOfDoor->number) }}" placeholder="Enter number" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.number_of_doors.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
