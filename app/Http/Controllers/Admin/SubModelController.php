<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\SubModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Exception;

class SubModelController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routeName;

    public function __construct()
    {
        $this->model = SubModel::class;
        $this->viewPath = 'backend.sub_models';
        $this->routeName = 'admin.sub_models.';
    }

    // List SubModels with DataTable
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ($this->model)::with('carModel')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('car_model', fn ($data) => $data->carModel->name ?? '—')
                ->addColumn('name_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->name : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route($this->routeName . 'edit', $data->id) . '"
                               class="btn btn-primary text-white" title="Edit">
                               <i class="fa fa-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-danger" title="Delete"
                                onclick="showDeleteConfirm(' . $data->id . ')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['name_fr', 'action'])
                ->make();
        }

        return view("{$this->viewPath}.index");
    }

    // Show create form
    public function create(): View
    {
        $carModels = CarModel::all();
        return view("{$this->viewPath}.create", compact('carModels'));
    }

    // Store new SubModel
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_fr'     => 'required|string|max:100',
                'car_model_id' => 'required|exists:car_models,id',
                'name' => 'required|string|max:100|unique:sub_models,name',
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            $subModel = ($this->model)::create($request->only(['car_model_id', 'name']));

            $subModel->translations()->create([
                'language' => 'fr',
                'name'     => $request->name_fr,
            ]);

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'SubModel created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'SubModel creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $subModel = ($this->model)::with('translations')->findOrFail($id);
        $carModels = CarModel::all();
        return view("{$this->viewPath}.edit", compact('subModel', 'carModels'));
    }

    // Update SubModel
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $subModel = ($this->model)::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name_fr'      => 'required|string|max:100',
                'car_model_id' => 'required|exists:car_models,id',
                'name'         => 'required|string|max:100|unique:sub_models,name,' . $subModel->id,
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            // 1. Main SubModel table update
            $subModel->update($request->only(['car_model_id', 'name']));

            // 2. Translation update logic (updateOrCreate bebohar korun)
            $subModel->translations()->updateOrCreate(
                [
                    'language' => 'fr', // Ei condition check korbe row ache ki na
                ],
                [
                    'name'     => $request->name_fr, // Row thakle name update hobe, na thakle create hobe
                ]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'SubModel updated successfully.');

        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'SubModel update failed.');
        }
    }

    // Delete SubModel
    public function destroy($id): JsonResponse
    {
        try {
            $subModel = ($this->model)::findOrFail($id);
            $subModel->delete();

            return response()->json([
                'success' => true,
                'message' => 'SubModel deleted successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete SubModel. ' . $e->getMessage(),
            ], 500);
        }
    }
}
