@extends('backend.app')

@section('title', 'Edit Category')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4 card">
                        <h4 class="mb-4">Edit Category</h4>

                        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">English Category Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name_fr" class="form-label">French Category Name</label>
                                @php
                                    // Jodi table-e 'locale' column thake
                                    $fr_translation = $category->translations->where('language', 'fr')->first();
                                    // Jodi table-e 'idlanguagename' ba 'language_id' thake, tobe niche 'locale' er bodole seta likhun
                                    $fr_name = $fr_translation ? $fr_translation->name : '';
                                @endphp
                                <input type="text" name="name_fr" id="name_fr" class="form-control"
                                    value="{{ old('name_fr', $fr_name) }}" required>
                                @error('name_fr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Category</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
