@extends('backend.app')

@section('title', 'Add Sub Model')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="p-4 card">

                        <h4 class="mb-3">Add Sub Model</h4>

                        <form action="{{ route('admin.sub_models.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="car_model_id" class="form-label">Car Model</label>
                                <select name="car_model_id" id="car_model_id" class="form-control" required>
                                    <option value="">Select Car Model</option>
                                    @foreach ($carModels as $carModel)
                                        <option value="{{ $carModel->id }}"
                                            {{ old('car_model_id') == $carModel->id ? 'selected' : '' }}>
                                            {{ $carModel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Sub Model Name (En)</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">Sub Model Name (Fr)</label>
                                <input type="text" name="name_fr" id="name_fr" class="form-control"
                                    value="{{ old('name_fr') }}" required>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ route('admin.sub_models.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
