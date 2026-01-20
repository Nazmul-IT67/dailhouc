<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\Cylinder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class CylinderController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.cylinders.';
    protected string $routeName = 'admin.cylinders.';

    // Display all cylinders
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Cylinder::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('number', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new cylinder
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'number' => 'required|integer|unique:cylinders,number',
        ]);

        try {
            Cylinder::create(['number' => $request->number]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Cylinder added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add cylinder.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $cylinder = Cylinder::findOrFail($id);
        return view($this->viewPath . 'edit', compact('cylinder'));
    }

    // Update cylinder
    public function update(Request $request, $id): RedirectResponse
    {
        $cylinder = Cylinder::findOrFail($id);

        $request->validate([
            'number' => 'required|integer|unique:cylinders,number,' . $cylinder->id,
        ]);

        try {
            $cylinder->update(['number' => $request->number]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Cylinder updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update cylinder.');
        }
    }

    // Delete cylinder
    public function destroy($id): JsonResponse
    {
        $cylinder = Cylinder::findOrFail($id);
        $cylinder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cylinder deleted successfully!',
        ]);
    }
}
