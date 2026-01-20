@extends('backend.app')

@section('title', 'Add Number of Door')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card p-4">
                        <h4 class="mb-4">Add New Number of Door</h4>
                        <form action="{{ route('admin.number_of_doors.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="number" class="form-label">Number  </label>
                                <input type="number" name="number" id="number"
                                    class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}"
                                    placeholder="Enter number" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.number_of_doors.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
