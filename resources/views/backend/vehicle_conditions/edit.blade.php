@extends('backend.app')

@section('title', 'Edit Vehicle Condition')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card p-4">
                        <h4 class="mb-4">Edit Vehicle Condition</h4>

                        <form action="{{ route('admin.vehicle_conditions.update', $vehicleCondition->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Condition Name (En)<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $vehicleCondition->name) }}" placeholder="Enter condition name"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Condition Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror"
                                    value="{{ old('name_fr', $vehicleCondition->translations->where('language', 'fr')->first()->name ?? '') }}" 
                                    placeholder="Enter condition name" required>
                                
                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.vehicle_conditions.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Condition</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
