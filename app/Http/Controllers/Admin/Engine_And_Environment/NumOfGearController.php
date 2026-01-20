<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\NumOfGear;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class NumOfGearController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.num_of_gears.';
    protected string $routeName = 'admin.num_of_gears.';

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = NumOfGear::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('number', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', fn($data) => '
                    <div class="btn-group btn-group-sm">
                        <a href="' . route($this->routeName . 'edit', $data->id) . '" class="btn btn-primary">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <button type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                ')
                ->rawColumns(['action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'number' => 'required|string|max:10|unique:num_of_gears,number',
        ]);

        try {
            NumOfGear::create(['number' => $request->number]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Number of gears added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add number of gears.');
        }
    }

    public function edit($id): View
    {
        $numOfGear = NumOfGear::findOrFail($id);
        return view($this->viewPath . 'edit', compact('numOfGear'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $numOfGear = NumOfGear::findOrFail($id);

        $request->validate([
            'number' => 'required|string|max:10|unique:num_of_gears,number,' . $numOfGear->id,
        ]);

        try {
            $numOfGear->update(['number' => $request->number]);
            return redirect()->route($this->routeName . 'index')->with('success', 'Number of gears updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update number of gears.');
        }
    }

    public function destroy($id): JsonResponse
    {
        $numOfGear = NumOfGear::findOrFail($id);
        $numOfGear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Number of gears deleted successfully!',
        ]);
    }
}
