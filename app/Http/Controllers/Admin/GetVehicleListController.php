<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GetVehicleListController extends Controller
{
    protected string $viewPath = 'backend.vehicles.';
    protected string $routeName = 'admin.vehicles.';

    // Display vehicles list
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vehicles = Vehicle::with(['user', 'category', 'brand', 'model'])->latest();

            return DataTables::of($vehicles)
                ->addIndexColumn()
                ->addColumn('category', fn($vehicle) => $vehicle->category?->name ?? '-')
                ->addColumn('user', fn($vehicle) => $vehicle->user?->name ?? '-')
                ->addColumn('brand', fn($vehicle) => $vehicle->brand?->name ?? '-')
                ->addColumn('model', fn($vehicle) => $vehicle->model?->name ?? '-')
                ->addColumn('date', function ($vehicle) {
                    return $vehicle->created_at ? $vehicle->created_at->format('d M Y') : '-';
                })
                ->addColumn('status', function ($vehicle) {
                    $checked = $vehicle->status ? 'checked' : '';
                    return '
                            <label class="toggle_switch">
                                <input type="checkbox" class="toggle-status" data-id="' . $vehicle->id . '" ' . $checked . '>
                                <span class="slider round"></span>
                            </label>
                        ';
                })
                ->addColumn('featured', function ($vehicle) {
                    $checked = $vehicle->is_featured ? 'checked' : '';
                    return '
                        <label class="toggle_switch">
                            <input type="checkbox" class="toggle-feature" data-id="' . $vehicle->id . '" ' . $checked . '>
                            <span class="slider round"></span>
                        </label>
                    ';
                })
                ->addColumn('featured_request', function ($vehicle) {
                    if ($vehicle->featured_request) {
                        return '<button class="btn btn-warning btn-sm">Yes</button>';
                    }
                    return '<button class="btn btn-secondary btn-sm">No</button>';
                })
                ->rawColumns(['status', 'featured', 'action', 'featured_request'])


                ->addColumn('action', function ($vehicle) {
                    $viewUrl = route('admin.vehicles.show', $vehicle->id);
                    return '
                    <a href="' . $viewUrl . '" style="background-color: #006666 !important;" class="btn btn-info btn-sm me-1">View</a>
                    <button onclick="deleteVehicle(' . $vehicle->id . ')" class="btn btn-danger btn-sm">Delete</button>
                ';
                })
                ->rawColumns(['status', 'featured', 'featured_request', 'action']) // 👈 IMPORTANT: allow status to render as HTML
                ->make(true);
        }

        return view($this->viewPath . 'index');
    }


    // Delete vehicle
    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Vehicle not found']);
        }

        $vehicle->delete();

        return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully']);
    }
    public function show($id)
    {
        // Load vehicle with all related data
        $vehicle = Vehicle::with([
            'data.condition',
            'data.bodyColor',
            'data.upholstery',
            'data.interiorColor',
            'data.previousOwner',
            'data.numOfDoor',
            'data.numOfSeats',
            'photos',
            'engineAndEnvironment.driverType',
            'engineAndEnvironment.transmission',
            'engineAndEnvironment.numOfGears',
            'engineAndEnvironment.cylinders',
            'engineAndEnvironment.emissionClass',
            'conditionAndMaintenance',
            'category',
            'brand',
            'model',
            'fuel',
            'body_type',
            'transmission',
            'power',
            'equipment_line',
            'seller_type',
        ])->findOrFail($id);

        return view($this->viewPath . 'show', compact('vehicle'));
    }
    // public function updateStatus(Request $request, $id)
    // {

    //     $vehicle = Vehicle::findOrFail($id);

    //     $vehicle->status = $request->status;
    //     $vehicle->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Vehicle status updated successfully.'
    //     ]);
    // }
    public function updateStatus(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->status = $request->status;
        $vehicle->save();

        if ($vehicle->user) {
            $statusText = $vehicle->status ? 'Active' : 'Inactive';

            $vehicle->user->notify(new GeneralNotification(
                'Vehicle Status Updated',
                'Your vehicle "' . ($vehicle->brand?->name ?? 'Unknown') . ' ' . ($vehicle->model?->name ?? '') . '" has been marked as ' . $statusText
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicle status updated successfully and user notified.'
        ]);
    }
    public function updateFeature(Vehicle $vehicle, Request $request)
    {
        $vehicle->is_featured = $request->is_featured;
        $vehicle->save();
 
        if ($vehicle->user) {
            $featureText = $vehicle->is_featured ? 'Featured' : 'Unfeatured';

            $vehicle->user->notify(new GeneralNotification(
                'Vehicle Feature Status Updated',
                'Your vehicle "' . ($vehicle->brand?->name ?? 'Unknown') . ' ' . ($vehicle->model?->name ?? '') . '" has been marked as ' . $featureText
            ));
        }


        return response()->json([
            'success' => true,
            'message' => 'Vehicle featured status updated successfully.'
        ]);
    }
}
