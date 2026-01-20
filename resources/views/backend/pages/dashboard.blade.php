@extends('backend.app')
@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4>Dashboard</h4>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('backend/svg/icon-sprite.svg#stroke-home') }}"></use>
                                    </svg>
                                </a>
                            </li>
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item active">Default</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard cards -->
        <div class="container-fluid">
            @php
                // সব Vehicle
                $allVehicles = \App\Models\Vehicle::count();

                // শুধু pending vehicle ধরো যদি status কলাম থাকে
                $pendingVehicles = \App\Models\Vehicle::where('status', 1)->count();
            @endphp

            <div class="row g-4">
                <!-- Pending Vehicle -->
                <div class="col-md-3">
                    <div class="card stat-card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <div class="icon-circle text-warning mb-3 mx-auto bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M4 17v2h16v-2zM5 6h14l1 9H4z" />
                                    <path d="M9 18a2 2 0 1 0 0 4a2 2 0 0 0 0 -4zM15 18a2 2 0 1 0 0 4a2 2 0 0 0 0 -4z" />
                                </svg>
                            </div>
                            <h6 class="text-muted">All Pending Vehicle</h6>
                            <h3 class="fw-bold mb-0">{{ $pendingVehicles }}</h3>
                            <p class="text-danger small mb-0">Pending Approval</p>
                        </div>
                    </div>
                </div>
                <!-- Orders -->
                <div class="col-md-3">
                    <div class="card stat-card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <div class="icon-circle text-danger mb-3 mx-auto bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M17.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0" />
                                    <path d="M6 8v11a1 1 0 0 0 1.806 .591l3.694 -5.091v.055" />
                                    <path
                                        d="M6 8h15l-3.5 7l-7.1 -.747a4 4 0 0 1 -3.296 -2.493l-2.853 -7.13a1 1 0 0 0 -.928 -.63h-1.323" />
                                </svg>
                            </div>
                            <h6 class="text-muted">All Vehicles</h6>
                            <h3 class="fw-bold mb-0">{{ $allVehicles }}</h3>
                            <p class="text-danger small mb-0">All Vehicle</p>
                        </div>
                    </div>
                </div>
                <!-- Revenue -->
                {{-- <div class="col-md-3">
                    <div class="card stat-card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <div class="icon-circle text-success mb-3 mx-auto bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 17c0 -6.075 -5.373 -11 -12 -11" />
                                    <path d="M3 3v18h18" />
                                </svg>
                            </div>
                            <h6 class="text-muted">Revenue</h6>
                            <h3 class="fw-bold mb-0">$12,400</h3>
                            <p class="text-success small mb-0">+12% this month</p>
                        </div>
                    </div>
                </div>
                <!-- Notifications -->
                <div class="col-md-3">
                    <div class="card stat-card shadow-sm border-0 rounded-4">
                        <div class="card-body text-center">
                            <div class="icon-circle text-info mb-3 mx-auto bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                </svg>
                            </div>
                            <h6 class="text-muted">Notifications</h6>
                            <h3 class="fw-bold mb-0">18</h3>
                            <p class="text-muted small mb-0">Last 24 hours</p>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>

        <style>
            .stat-card {
                background: #fff;
                transition: all 0.3s ease-in-out;
            }

            .stat-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            }

            .icon-circle {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 1.3rem;
                box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.08);
            }
        </style>
    </div>
@endsection
