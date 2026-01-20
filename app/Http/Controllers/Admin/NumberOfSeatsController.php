<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NumberOfSeat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class NumberOfSeatsController extends Controller
{
    protected string $viewPath = 'backend.number_of_seats.';
    protected string $routeName = 'admin.number_of_seats.';

    // Display all number of seats
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = NumberOfSeat::latest();

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

    // Store new number of seats
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|unique:number_of_seats,number',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            NumberOfSeat::create($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Number of seats created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $seat = NumberOfSeat::findOrFail($id);
        return view($this->viewPath . 'edit', compact('seat'));
    }

    // Update number of seats
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $seat = NumberOfSeat::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|unique:number_of_seats,number,' . $seat->id,
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $seat->update($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Number of seats updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Update failed.');
        }
    }

    // Delete number of seats
    public function destroy($id): JsonResponse
    {
        $seat = NumberOfSeat::findOrFail($id);
        $seat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Number of seats deleted successfully!',
        ]);
    }
}
