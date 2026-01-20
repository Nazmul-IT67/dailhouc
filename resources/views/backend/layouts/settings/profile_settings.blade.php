@extends('backend.app')

@section('title', 'Profile settings')

@section('content')
    <div class="page-body">
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-style settings-card-1 mb-30">
                        <div class="title mb-30 d-flex justify-content-between align-items-center">
                            <h4>My Profile</h4>
                        </div>

                        <div class="profile-info">
                            <!-- Profile Image Upload -->
                            <div class="d-flex justify-content-center align-items-center mt-3 mb-4">
                                <div class="text-center">
                                    <!-- Profile Picture -->
                                    <img id="profile-picture"
                                        src="{{ asset(Auth::user()->avatar ?? 'backend/images/profile.png') }}"
                                        alt="Profile Picture" class="rounded-circle shadow" width="100" height="100"
                                        style="object-fit: cover; border: 3px solid #fff;">

                                    <!-- Upload Button -->
                                    <div class="update-image mt-3">
                                        <input type="file" name="profile_picture" id="profile_picture_input"
                                            class="d-none">
                                        <label for="profile_picture_input" class="btn btn-sm btn-outline-primary">
                                            <i class="lni lni-cloud-upload me-1"></i> Change Photo
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <!-- Update Profile -->
                                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                                    <div class="card card-body h-100">
                                        <h4 class="mb-4">Update Profile</h4>
                                        <form method="POST" action="{{ route('admin.update.profile') }}">
                                            @csrf
                                            <div class="input-style-1 mb-4">
                                                <label for="name">User Name</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    id="name" value="{{ Auth::user()->name }}"
                                                    placeholder="Full Name" />
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="input-style-1 mb-4">
                                                <label for="email">Email</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    id="email" value="{{ Auth::user()->email }}" placeholder="Email" />
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100">
                                                Update Profile
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Change Password -->
                                <div class="col-12 col-lg-6">
                                    <div class="card card-body h-100">
                                        <h4 class="mb-4">Change Password</h4>
                                        <form method="POST" action="{{ route('admin.update.Password') }}">
                                            @csrf
                                            <div class="input-style-1 mb-4">
                                                <label for="old_password">Current Password</label>
                                                <input type="password"
                                                    class="form-control @error('old_password') is-invalid @enderror"
                                                    name="old_password" id="old_password" placeholder="Current Password" />
                                                @error('old_password')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="input-style-1 mb-4">
                                                <label for="password">New Password</label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" id="password" placeholder="New Password" />
                                                @error('password')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="input-style-1 mb-4">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="password"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    name="password_confirmation" id="password_confirmation"
                                                    placeholder="Confirm Password" />
                                                @error('password_confirmation')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Change Password
                                                </button>
                                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger w-100">
                                                    Cancel
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> <!-- row -->
                        </div> <!-- profile-info -->
                    </div> <!-- card-style -->
                </div> <!-- col-lg-12 -->
            </div> <!-- row -->
        </div> <!-- container-fluid -->
    </div> <!-- page-body -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#profile_picture_input').change(function() {
                const formData = new FormData();
                formData.append('profile_picture', $(this)[0].files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('admin.update.profile.picture') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            $('#profile-picture').attr('src', data.image_url);
                            toastr.success('Profile picture updated successfully.');
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong while uploading.');
                    }
                });
            });
        });
    </script>
@endpush
