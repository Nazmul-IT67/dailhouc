<?php

namespace App\Http\Controllers\Admin;

use App\Models\ModelYear;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Support\Facades\Validator;

class ModelYearController extends Controller
{
    protected string $viewPath = 'backend.model_years.';
    protected string $routeName = 'admin.model_years.';

    // Index
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ModelYear::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('year', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
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

    // Store new year of seats
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'year' => 'required|integer|unique:model_years,year',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            ModelYear::create($request->only('year'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Creation failed.');
        }
    }

    // Delete number of seats
    public function destroy($id): JsonResponse
    {
        $seat = ModelYear::findOrFail($id);
        $seat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Number of seats deleted successfully!',
        ]);
    }
}
