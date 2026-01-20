<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\AxleCount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class AxleCountController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.axle_counts.';
    protected string $routeName = 'admin.axle-counts.';

    // Display all axle counts
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = AxleCount::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('count', 'LIKE', "%$searchTerm%");
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

    // Store new axle count
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'count' => 'required|integer|unique:axle_counts,count|min:1',
        ]);

        try {
            AxleCount::create([
                'count' => $request->count,
            ]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Axle Count added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Axle Count.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $axleCount = AxleCount::findOrFail($id);
        return view($this->viewPath . 'edit', compact('axleCount'));
    }

    // Update axle count
    public function update(Request $request, $id): RedirectResponse
    {
        $axleCount = AxleCount::findOrFail($id);

        $request->validate([
            'count' => 'required|integer|unique:axle_counts,count,' . $axleCount->id . '|min:1',
        ]);

        try {
            $axleCount->update([
                'count' => $request->count,
            ]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Axle Count updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Axle Count.');
        }
    }

    // Delete axle count
    public function destroy($id): JsonResponse
    {
        $axleCount = AxleCount::findOrFail($id);
        $axleCount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Axle Count deleted successfully!',
        ]);
    }
}
