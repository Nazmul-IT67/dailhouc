@extends('backend.app')

@section('title', 'Edit Sub Model')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="p-4 card">
                        <h4 class="mb-3">Edit Sub Model</h4>
                        <form action="{{ route('admin.sub_models.update', $subModel->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="car_model_id" class="form-label">Car Model</label>
                                <select name="car_model_id" id="car_model_id" class="form-control" required>
                                    <option value="">Select Car Model</option>
                                    @foreach ($carModels as $carModel)
                                        <option value="{{ $carModel->id }}"
                                            {{ $subModel->car_model_id == $carModel->id ? 'selected' : '' }}>
                                            {{ $carModel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Sub Model Name (En)</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ $subModel->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Sub Model Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr"
                                    class="form-control @error('name_fr') is-invalid @enderror" {{-- Ekhane $subModel hobe, $carModel noy --}}
                                    value="{{ old('name_fr', $subModel->translations->where('language', 'fr')->first()->name ?? '') }}"
                                    placeholder="Enter sub model name in French" required>

                                @error('name_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.sub_models.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
