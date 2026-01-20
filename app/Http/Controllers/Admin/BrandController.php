<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class BrandController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routeName;

    public function __construct()
    {
        $this->model = Brand::class;
        $this->viewPath = 'backend.brands';
        $this->routeName = 'admin.brands.';
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ($this->model)::with('category')->latest();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category', fn($data) => $data->category->name ?? '—')
                ->addColumn('name_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->name : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('logo', function ($data) {
                    if ($data->logo) {
                        // $logoPath = str_replace('public/', '', $data->logo);
                        return '<img src="' . asset($data->logo) . '" height="30">';
                    }
                    return '—';
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


                ->rawColumns(['name_fr', 'action', 'logo'])
                ->make();
        }

        return view("{$this->viewPath}.index");
    }

    public function create(): View
    {
        $categories = Category::all();
        return view("{$this->viewPath}.create", compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:100|unique:brands,name',
                'name_fr'     => 'required|string|max:100',
                'category_id' => 'required|exists:categories,id',
                'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            $data = $request->only(['name', 'category_id']);

            if ($request->hasFile('logo')) {
                $data['logo'] = uploadImage($request->file('logo'), 'Brand');
            }

            $brand = ($this->model)::create($data);

            $brand->translations()->create([
                'language' => 'fr', 
                'name'     => $request->name_fr,
            ]);

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Brand created successfully with French translation.');

        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Brand creation failed: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        $brand = ($this->model)::with('translations')->findOrFail($id);
        $categories = Category::all();
        return view("{$this->viewPath}.edit", compact('brand', 'categories'));
    }


    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $brand = ($this->model)::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:100|unique:brands,name,' . $brand->id,
                'name_fr'     => 'required|string|max:100',
                'category_id' => 'required|exists:categories,id',
                'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return back()->with('t-validation', $validator->errors()->all())->withInput();
            }

            $data = $request->only(['name', 'category_id']);

            if ($request->hasFile('logo')) {
                $oldLogo = $brand->logo;
                $newLogoPath = uploadImage($request->file('logo'), 'Brand');

                if ($newLogoPath) {
                    $data['logo'] = $newLogoPath;
                    $oldImagePath = public_path($oldLogo);
                    if ($oldLogo && file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            $brand->update($data);
            $brand->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Brand updated successfully.');

        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Brand update failed.');
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $brand = ($this->model)::findOrFail($id);

            if ($brand->logo) {
                $logoPath = public_path($brand->logo);
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }
            $brand->delete();

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Brand. ' . $e->getMessage(),
            ], 500);
        }
    }
}
