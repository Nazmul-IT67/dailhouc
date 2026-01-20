<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleCondition;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class VehicleConditionController extends Controller
{
    // Display all vehicle conditions
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = VehicleCondition::latest();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->name : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('admin.vehicle_conditions.edit', ['id' => $data->id]) . '"
                               class="text-white btn btn-primary" title="Edit">
                               <i class="fa fa-pencil"></i>
                            </a>
                            <button onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger text-white">
                               <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['name_fr', 'action'])
                ->make();
        }

        return view('backend.vehicle_conditions.index');
    }

    // Show create form
    public function create(): View
    {
        return view('backend.vehicle_conditions.create');
    }

    // Store new vehicle condition
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:vehicle_conditions,name',
                'name_fr' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $vehicleCondition = VehicleCondition::create($request->only(['name']));
            $vehicleCondition->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );
            return redirect()->route('admin.vehicle_conditions.index')
                ->with('success', 'Vehicle Condition created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.vehicle_conditions.index')
                ->with('error', 'Vehicle Condition creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $vehicleCondition = VehicleCondition::with('translations')->findOrFail($id);
        return view('backend.vehicle_conditions.edit', compact('vehicleCondition'));
    }

    // Update vehicle condition
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $vehicleCondition = VehicleCondition::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:vehicle_conditions,name,' . $vehicleCondition->id,
                'name_fr' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $vehicleCondition->update($request->only(['name']));
            $vehicleCondition->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route('admin.vehicle_conditions.index')
                ->with('success', 'Vehicle Condition updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.vehicle_conditions.index')
                ->with('error', 'Vehicle Condition update failed.');
        }
    }

    // Delete vehicle condition
    public function destroy($id): JsonResponse
    {
        $vehicleCondition = VehicleCondition::findOrFail($id);
        $vehicleCondition->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle Condition deleted successfully!',
        ]);
    }
}
