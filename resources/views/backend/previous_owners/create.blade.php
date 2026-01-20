@extends('backend.app')

@section('title', 'Add Previous Owner')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card p-4">
                        <h4 class="mb-4">Add New Previous Owner Option</h4>

                        <form action="{{ route('admin.previous_owners.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="number" class="form-label">Number of Owners <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="number" id="number"
                                    class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}"
                                    placeholder="Enter number" min="1" required>
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.previous_owners.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
