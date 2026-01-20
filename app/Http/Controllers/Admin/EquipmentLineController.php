<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentLine;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class EquipmentLineController extends Controller
{
    protected string $viewPath = 'backend.equipment_lines.';
    protected string $routeName = 'admin.equipment_lines.';

    // Index
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = EquipmentLine::latest();

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

    // Create
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title_fr' => 'required|string|max:100',
            'title'    => 'required|string|max:255|unique:equipment_lines,title',
        ]);

        try {
            $data = EquipmentLine::create(['title' => $request->title]);
            $data->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Equipment Line added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Equipment Line.');
        }
    }

    // Edit
    public function edit($id): View
    {
        $equipmentLine = EquipmentLine::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('equipmentLine'));
    }

    // Update
    public function update(Request $request, $id): RedirectResponse
    {
        $equipmentLine = EquipmentLine::findOrFail($id);

        $request->validate([
            'title_fr' => 'required|string|max:100',
            'title'    => 'required|string|max:255|unique:equipment_lines,title,' . $equipmentLine->id,
        ]);

        try {
            $equipmentLine->update(['title' => $request->title]);
            $equipmentLine->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Equipment Line updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Equipment Line.');
        }
    }

    // Delete
    public function destroy($id): JsonResponse
    {
        $equipmentLine = EquipmentLine::findOrFail($id);
        $equipmentLine->delete();
        return response()->json([
            'success' => true,
            'message' => 'Equipment Line deleted successfully!',
        ]);
    }
}
