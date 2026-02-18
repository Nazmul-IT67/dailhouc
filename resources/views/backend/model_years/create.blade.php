@extends('backend.app')

@section('title', 'Add New')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card p-4">
                        <h4 class="mb-4">Add New</h4>

                        <form action="{{ route('admin.model_years.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="year" class="form-label">Number  </label>
                                <input type="number" name="year" min="1950" max="2050" class="form-control @error('year') is-invalid @enderror" value="{{ old('year') }} placeholder="YYYY">    
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.model_years.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
