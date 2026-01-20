@extends('backend.app')

@section('title', 'Add Vehicle Condition')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card p-4">
                        <h4 class="mb-4">Add New Vehicle Condition</h4>

                        <form action="{{ route('admin.vehicle_conditions.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Condition Name (En)<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Enter condition name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Condition Name (Fr)<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror" value="{{ old('name_fr') }}"
                                    placeholder="Enter condition name" required>
                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.vehicle_conditions.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Condition</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
