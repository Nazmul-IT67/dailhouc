@extends('backend.app')

@section('title', 'Edit City')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="p-5 card">
                        <h4>Edit City</h4>
                        @if (session('t-validation'))
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: '{!! implode('<br>', session('t-validation')) !!}'
                                });
                            </script>
                        @endif
                        <form action="{{ route('admin.cities.update', $city->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">City Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $city->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select name="country_id" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ old('country_id', $city->country_id) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
