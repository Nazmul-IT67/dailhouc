<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\DriverType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class DriverTypeController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.driver_types.';
    protected string $routeName = 'admin.driver_types.';

    // Display all driver types
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = DriverType::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->title : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route($this->routeName . 'edit', $data->id) . '" class="btn btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['title_fr', 'action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new driver type
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'    => 'required|string|max:255|unique:driver_types,title',
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $driverType = DriverType::create(['title' => $request->title]);
            $driverType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Driver type added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add driver type.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $driverType = DriverType::findOrFail($id);
        return view($this->viewPath . 'edit', compact('driverType'));
    }

    // Update driver type
    public function update(Request $request, $id): RedirectResponse
    {
        $driverType = DriverType::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:255|unique:driver_types,title,' . $driverType->id,
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $driverType->update(['title' => $request->title]);
            $driverType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Driver type updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update driver type.');
        }
    }

    // Delete driver type
    public function destroy($id): JsonResponse
    {
        $driverType = DriverType::findOrFail($id);
        $driverType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Driver type deleted successfully!',
        ]);
    }
}
