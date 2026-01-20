<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BodyType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class BodyTypeController extends Controller
{
    protected string $viewPath = 'backend.body_types.';
    protected string $routeName = 'admin.body_types.';

    // Display all body types
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = BodyType::with('translations')->get();
            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->title : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('category', function ($data) {
                    return $data->category->name ?? '-';
                })
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
                ->rawColumns(['title_fr', 'action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        $categories = Category::all();
        return view($this->viewPath . 'create', compact('categories'));
    }

    // Store new body type
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title_fr'    => 'required|string|max:100',
            'title'       => 'required|string|max:255|unique:body_types,title',
            'icon'        => 'nullable|mimes:png,jpg,jpeg,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            $data = $request->only(['title', 'category_id']);

            if ($request->hasFile('icon')) {
                $data['icon'] = uploadImage($request->file('icon'), 'body_type_icons');
            }

            $body = BodyType::create($data);
            $body->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Body Type added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Body Type.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $bodyType = BodyType::findOrFail($id);
        $categories = Category::all();

        return view($this->viewPath . 'edit', compact('bodyType', 'categories'));
    }

    // Update body type
    public function update(Request $request, $id): RedirectResponse
    {
        $bodyType = BodyType::findOrFail($id);

        $request->validate([
            'title_fr'    => 'required|string|max:100',
            'title'       => 'required|string|max:255|unique:body_types,title,' . $bodyType->id,
            'category_id' => 'required|exists:categories,id',
            'icon'        => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        try {
            $data = $request->only(['title', 'category_id']);

            // If new icon uploaded, delete old one
            if ($request->hasFile('icon')) {
                if ($bodyType->icon && file_exists(public_path($bodyType->icon))) {
                    unlink(public_path($bodyType->icon));
                }

                $data['icon'] = uploadImage($request->file('icon'), 'body_type_icons');
            }

            $bodyType->update($data);
            $bodyType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Body Type updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Body Type.');
        }
    }

    // Delete body type
    public function destroy($id): JsonResponse
    {
        $bodyType = BodyType::findOrFail($id);
        $bodyType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Body Type deleted successfully!',
        ]);
    }
}
