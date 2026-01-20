@extends('backend.app')

@section('title', 'Add Currency')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="p-5 card">
                        <h4>Add New Currency</h4>

                        {{-- Display Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.currencies.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Code</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Symbol</label>
                                <input type="text" name="symbol" class="form-control" value="{{ old('symbol') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Exchange Rate</label>
                                <input type="number" step="0.01" name="exchange_rate" class="form-control"
                                    value="{{ old('exchange_rate') }}" required>
                            </div>
                            {{-- <div class="mb-3 form-check">
                                <input type="checkbox" name="is_default" class="form-check-input" id="is_default"
                                    value="1" {{ $currency->is_default ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">Set as Default Currency</label>
                            </div> --}}

                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
