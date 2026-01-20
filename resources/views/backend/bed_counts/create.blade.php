@extends('backend.app')

@section('title', 'Add Bed Count')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card p-4">
                        <h4 class="mb-4">Add Bed Count</h4>
                        <form action="{{ route('admin.bed_counts.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="number" class="form-label">Number of Beds (En)</label>
                                <input type="text" name="number" id="number" class="form-control"
                                    value="{{ old('number') }}" required>
                                @error('number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="number_fr" class="form-label">Number of Beds (Fr)</label>
                                <input type="text" name="number_fr" id="number_fr" class="form-control"
                                    value="{{ old('number_fr') }}" required>
                                @error('number_fr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Save Bed Count</button>
                            <a href="{{ route('admin.bed_counts.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
