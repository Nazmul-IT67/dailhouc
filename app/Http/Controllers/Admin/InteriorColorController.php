<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InteriorColor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Exception;

class InteriorColorController extends Controller
{
    protected string $viewPath = 'backend.interior_colors.';
    protected string $routeName = 'admin.interior_colors.';

    // Display all interior colors
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = InteriorColor::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->name : '<span class="text-muted">No Translation</span>';
                })
                ->addColumn('color_code', function ($data) {
                    return '<div style="width:30px; height:30px; background:' . $data->color_code . '; border:1px solid #000;"></div>';
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm">
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
                ->rawColumns(['name_fr', 'action', 'color_code'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new interior color
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'       => 'required|string|max:100|unique:interior_colors,name',
                'name_fr'    => 'nullable|string|max:100',
                'color_code' => 'nullable|string|max:7',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $interiorColor = InteriorColor::create($request->only(['name', 'color_code']));
            $interiorColor->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Interior Color created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Interior Color creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $interiorColor = InteriorColor::findOrFail($id);
        return view($this->viewPath . 'edit', compact('interiorColor'));
    }

    // Update interior color
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $interiorColor = InteriorColor::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'       => 'required|string|max:100|unique:interior_colors,name,' . $interiorColor->id,
                'name_fr'    => 'nullable|string|max:100',
                'color_code' => 'nullable|string|max:7',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $interiorColor->update($request->only(['name', 'color_code']));
            $interiorColor->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Interior Color updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Interior Color update failed.');
        }
    }

    // Delete interior color
    public function destroy($id): JsonResponse
    {
        $interiorColor = InteriorColor::findOrFail($id);
        $interiorColor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Interior Color deleted successfully!',
        ]);
    }
}
