<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class FuelController extends Controller
{
    protected string $viewPath = 'backend.fuels.';
    protected string $routeName = 'admin.fuels.';

    // Display all fuels
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Fuel::latest();

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

    // Store new fuel
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'          => 'required|string|max:255|unique:fuels,title',
            'title_fr'       => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'description_fr' => 'nullable|string',
        ]);

        try {
            $fuel = Fuel::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            if ($request->title_fr || $request->description_fr) {
                $fuel->translations()->updateOrCreate(
                    ['language' => 'fr'],
                    [
                        'title'       => $request->title_fr,
                        'description' => $request->description_fr,
                    ]
                );
            }

            return redirect()->route($this->routeName . 'index')->with('success', 'Fuel added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add fuel.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $fuel = Fuel::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('fuel'));
    }

    // Update fuel
    public function update(Request $request, $id): RedirectResponse
    {
        $fuel = Fuel::findOrFail($id);

        $request->validate([
            'title'          => 'required|string|max:255|unique:fuels,title,' . $fuel->id,
            'title_fr'       => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'description_fr' => 'nullable|string',
        ]);

        try {
            $fuel->update([
                'title'       => $request->title,
                'description' => $request->description,
            ]);

            if ($request->title_fr || $request->description_fr) {
                $fuel->translations()->updateOrCreate(
                    ['language' => 'fr'],
                    [
                        'title'       => $request->title_fr,
                        'description' => $request->description_fr,
                    ]
                );
            }

            return redirect()->route($this->routeName . 'index')->with('success', 'Fuel updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update fuel: ' . $e->getMessage());
        }
    }

    // Delete fuel
    public function destroy($id): JsonResponse
    {
        $fuel = Fuel::findOrFail($id);
        $fuel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fuel deleted successfully!',
        ]);
    }
}
