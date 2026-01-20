@extends('backend.app')

@section('title', 'View Vehicle')

@section('content')
    <div class="page-body">
        <div class="container-fluid">

            <div class="row mb-4">
                <div class="col-12">
                    <div class="p-3 rounded d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(90deg, #006666, #1cc88a); color: white;">
                        <h2 class="mb-0 text-white"><i class="fa fa-car me-2"></i>Vehicle Details</h2>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light">Back to List</a>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="card mb-4 p-4">
                <h4 class="mb-3">Basic Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Category</th>
                        <td>{{ $vehicle->category?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td>{{ $vehicle->user?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $vehicle->brand?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Model</th>
                        <td>{{ $vehicle->model?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fuel Type</th>
                        <td>{{ $vehicle->fuel?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Power</th>
                        <td>{{ $vehicle->power?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>First Registration</th>
                        <td>{{ $vehicle->first_registration ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Transmission</th>
                        <td>{{ $vehicle->transmission?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>{{ $vehicle->price ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Currency</th>
                        <td>
                            @if ($vehicle->price)
                                {{ $vehicle->currency?->symbol ?? '' }}
                                <small class="text-muted">({{ $vehicle->currency?->code ?? '' }})</small>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Milage</th>
                        <td>{{ $vehicle->milage ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Equipment Line</th>
                        <td>{{ $vehicle->equipment_line?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Seller Type</th>
                        <td>{{ $vehicle->seller_type?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if ($vehicle->status == 1)
                                <button class="btn btn-sm btn-success">Active</button>
                            @else
                                <button class="btn btn-sm btn-danger">Inactive</button>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Equipments</th>
                        <td>
                            @if ($vehicle->equipments->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach ($vehicle->equipments as $equipment)
                                        <li>{{ $equipment->title }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                </table>
            </div>

            <!-- Vehicle Details -->
            <div class="card mb-4 p-4">
                <h4 class="mb-3">Vehicle Details</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Vehicle Condition</th>
                        <td>{{ $vehicle->data?->condition?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Body Type</th>
                        <td>{{ $vehicle->body_type->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Body color</th>
                        <td>{{ $vehicle->data?->bodyColor?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Interior color</th>
                        <td>{{ $vehicle->data?->interiorColor?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Upholstery</th>
                        <td>{{ $vehicle->data?->upholstery?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Number of prev. Owner</th>
                        <td>{{ $vehicle->data?->previousOwner?->number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Number of Doors</th>
                        <td>{{ $vehicle->data?->numOfDoor?->number ?? '-' }}</td>

                    </tr>
                    <tr>
                        <th>Number of Seats</th>
                        <td>{{ $vehicle->data?->numOfSeats?->number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Metalic</th>
                        <td>{{ $vehicle->data?->metalic ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Negotiable</th>
                        <td>{{ $vehicle->data?->negotiable ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Indicate Vat</th>
                        <td>{{ $vehicle->data?->indicate_vat ? 'Yes' : 'No' }}</td>

                    </tr>


                </table>
            </div>

            <!-- Engine & Environment -->
            <div class="card mb-4 p-4">
                <h4 class="mb-3">Engine & Environment</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Driver Type</th>

                        <td>{{ $vehicle->engineAndEnvironment?->driverType?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Transmission</th>
                        <td>{{ $vehicle->transmission?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Number of Gears</th>
                        <td>{{ $vehicle->engineAndEnvironment?->numOfGears?->number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Cylinders</th>
                        <td>{{ $vehicle->engineAndEnvironment?->cylinders?->number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Emission Class</th>
                        <td>{{ $vehicle->engineAndEnvironment?->emissionClass?->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fuel Consump Urban</th>
                        <td>{{ $vehicle->engineAndEnvironment?->fuel_consumption_urban ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fuel Consump Combined</th>
                        <td>{{ $vehicle->engineAndEnvironment?->fuel_consumption_combined ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fuel Consump Combined/GM</th>
                        <td>{{ $vehicle->engineAndEnvironment?->fuel_consumption_combined_gm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Co2 Emissions</th>
                        <td>{{ $vehicle->engineAndEnvironment?->co2_emissions ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Co2 Emissions</th>
                        <td>{{ $vehicle->engineAndEnvironment?->co2_emissions ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catalytic Converter</th>
                        <td>{{ $vehicle->engineAndEnvironment?->catalytic_converter ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Particle Filter</th>
                        <td>{{ $vehicle->engineAndEnvironment?->particle_filter ? 'Yes' : 'No' }}</td>
                    </tr>

                </table>
            </div>

            <!-- Vehicle Photos -->
            @if ($vehicle->photos->isNotEmpty())
                <div class="card mb-4 p-4">
                    <h4 class="mb-3">Photos</h4>
                    <div class="row">
                        @foreach ($vehicle->photos as $photo)
                            <div class="col-md-3 mb-3">
                                <img src="{{ asset($photo->file_path) }}" class="img-fluid rounded shadow-sm"
                                    alt="Vehicle Photo">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
