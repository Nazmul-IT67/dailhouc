@extends('backend.app')

@section('title', 'Edit Bed Count')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card p-4">
                        <h4 class="mb-4">Edit Bed Count</h4>
                        <form action="{{ route('admin.bed_counts.update', $bedCount->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="number" class="form-label">Number of Beds</label>
                                <input type="text" name="number" id="number" class="form-control"
                                    value="{{ old('number', $bedCount->number) }}" required>
                                @error('number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="number_fr" class="form-label">Number of Beds (Fr)</label>
                                <input type="text" name="number_fr" id="number_fr"
                                    class="form-control @error('number_fr') is-invalid @enderror" {{-- .title er bodole .number hobe --}}
                                    value="{{ old('number_fr', $bedCount->translations->where('language', 'fr')->first()->number ?? '') }}"
                                    placeholder="Entrez le nombre en français">

                                @error('number_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update Bed Count</button>
                            <a href="{{ route('admin.bed_counts.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
