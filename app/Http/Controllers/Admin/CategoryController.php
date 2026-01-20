<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class CategoryController extends Controller
{
    // Display all categories
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = Category::with('translations')->latest();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->name : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('action', function ($data) {
                    return '
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="' . route('admin.categories.edit', ['id' => $data->id]) . '"
                           class="text-white btn btn-primary" title="Edit">
                           <i class="fa fa-pencil"></i>
                        </a>

                    </div>
                    ';
                })
                ->rawColumns(['name_fr', 'action'])
                ->make();
        }

        return view('backend.categories.index');
    }

    // Show create form
    public function create(): View
    {
        return view('backend.categories.create');
    }

    // Store new category
    public function store(Request $request): RedirectResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:categories,name',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            Category::create($request->only(['name']));
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Category creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $category = Category::with('translations')->findOrFail($id);
        return view('backend.categories.edit', compact('category'));
    }

    // Update category
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $category = Category::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:categories,name,' . $category->id,
                'name_fr' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $category->update([
                'name' => $request->name,
            ]);

            $category->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'   => $request->name_fr]
            );

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully with French translation.');

        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Category update failed: ' . $e->getMessage());
        }
    }

    // Delete category
    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!',
        ]);
    }
}
