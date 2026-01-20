<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\EmissionClass;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class EmissionClassController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.emission_classes.';
    protected string $routeName = 'admin.emission_classes.';

    // Display all emission classes
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = EmissionClass::latest();

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
        return view($this->viewPath . 'create');
    }

    // Store new emission class
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'    => 'required|string|max:255|unique:emission_classes,title',
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $emissionClass = EmissionClass::create(['title' => $request->title]);
            $emissionClass->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Emission class added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add emission class.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $emissionClass = EmissionClass::findOrFail($id);
        return view($this->viewPath . 'edit', compact('emissionClass'));
    }

    // Update emission class
    public function update(Request $request, $id): RedirectResponse
    {
        $emissionClass = EmissionClass::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:255|unique:emission_classes,title,' . $emissionClass->id,
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $emissionClass->update(['title' => $request->title]);
            $emissionClass->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Emission class updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update emission class.');
        }
    }

    // Delete emission class
    public function destroy($id): JsonResponse
    {
        $emissionClass = EmissionClass::findOrFail($id);
        $emissionClass->delete();

        return response()->json([
            'success' => true,
            'message' => 'Emission class deleted successfully!',
        ]);
    }
}
