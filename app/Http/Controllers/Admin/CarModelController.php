<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\View\View;

class CarModelController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routeName;

    public function __construct()
    {
        $this->model = CarModel::class;
        $this->viewPath = 'backend.car_models';
        $this->routeName = 'admin.car_models.';
    }

    public function index(Request $request): JsonResponse|view
    {
        if ($request->ajax()) {
            $data = ($this->model)::with('brand')->latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('brand', fn ($data) => $data->brand->name ?? '—')
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

    public function create(): view
    {
        $brands = Brand::all();
        return view("{$this->viewPath}.create", compact('brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_fr' => 'required|string|max:100',
                'brand_id' => 'required|exists:brands,id',
                'name' => 'required|string|max:100|unique:car_models,name',
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            $carModel = ($this->model)::create($request->only(['name', 'brand_id']));

            $carModel->translations()->create([
                'language' => 'fr',
                'name'     => $request->name_fr,
            ]);

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Car Model created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Car Model creation failed.');
        }
    }

    public function edit($id): view
    {
        $brands = Brand::all();
        $carModel = ($this->model)::with('translations')->findOrFail($id);
        return view("{$this->viewPath}.edit", compact('carModel', 'brands'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $carModel = ($this->model)::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name_fr'  => 'required|string|max:100',
                'brand_id' => 'required|exists:brands,id',
                'name'     => 'required|string|max:100|unique:car_models,name,' . $carModel->id,
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            // 1. Main table update
            $carModel->update($request->only(['name', 'brand_id']));

            // 2. Translation update (Structure thik korun)
            $carModel->translations()->updateOrCreate(
                [
                    'language' => 'fr', // Ei column ebong value diye database-e khujbe
                ],
                [
                    'name' => $request->name_fr // Jodi khuje pay tobe eta update hobe, na pele create hobe
                ]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Car Model updated successfully.');

        } catch (Exception $e) {
            // dd($e->getMessage()); // Debug korar jonno eita check korte paren
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Car Model update failed.');
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $carModel = ($this->model)::findOrFail($id);

            $carModel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Car Model deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Car Model. ' . $e->getMessage(),
            ], 500);
        }
    }
}
