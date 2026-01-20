<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NumberOfDoor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class NumberOfDoorController extends Controller
{
    protected string $viewPath = 'backend.number_of_doors.';
    protected string $routeName = 'admin.number_of_doors.';

    // Display all number of doors
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = NumberOfDoor::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('number', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route($this->routeName . 'edit', $data->id) . '"
                               class="btn btn-primary text-white" title="Edit">
                               <i class="fa fa-pencil"></i>
                            </a>
                            <button type="button" onclick="showDeleteConfirm(' . $data->id . ')"
                               class="btn btn-danger" title="Delete">
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

    // Store new number of doors
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|unique:number_of_doors,number',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            NumberOfDoor::create($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Number of doors added successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Failed to add number of doors.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $numberOfDoor = NumberOfDoor::findOrFail($id);
        return view($this->viewPath . 'edit', compact('numberOfDoor'));
    }

    // Update number of doors
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $numberOfDoor = NumberOfDoor::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|unique:number_of_doors,number,' . $numberOfDoor->id,
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $numberOfDoor->update($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Number of doors updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Failed to update number of doors.');
        }
    }

    // Delete number of doors
    public function destroy($id): JsonResponse
    {
        $numberOfDoor = NumberOfDoor::findOrFail($id);
        $numberOfDoor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Number of doors deleted successfully!',
        ]);
    }
}
