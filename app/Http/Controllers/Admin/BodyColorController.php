<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BodyColor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Exception;

class BodyColorController extends Controller
{
    protected $routeName = 'admin.body_colors.';
    protected $viewPath = 'backend.body_colors.';

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = BodyColor::latest();

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
                        <div class="btn-group btn-group-sm">
                            <a href="' . route($this->routeName . 'edit', $data->id) . '" class="btn btn-primary text-white">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['name_fr', 'action'])
                ->make();
        }

        return view("{$this->viewPath}index");
    }

    public function create(): View
    {
        return view("{$this->viewPath}create");
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name'       => 'required|string|max:100|unique:body_colors,name',
                'name_fr'    => 'nullable|string|max:100',
                'color_code' => 'nullable|string|max:7', // HEX code
            ]);

            $color = BodyColor::create($request->only(['name', 'color_code']));
            $color->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Body color created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')->with('error', 'Creation failed.');
        }
    }

    public function edit($id): View
    {
        $color = BodyColor::with('translations')->findOrFail($id);
        return view("{$this->viewPath}edit", compact('color'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $color = BodyColor::findOrFail($id);

            $request->validate([
                'name'       => 'required|string|max:100|unique:body_colors,name,' . $color->id,
                'name_fr'    => 'nullable|string|max:100',
                'color_code' => 'nullable|string|max:7',
            ]);

            $color->update($request->only(['name', 'color_code']));
            $color->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Body color updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')->with('error', 'Update failed.');
        }
    }

    public function destroy($id): JsonResponse
    {
        $color = BodyColor::findOrFail($id);
        $color->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
