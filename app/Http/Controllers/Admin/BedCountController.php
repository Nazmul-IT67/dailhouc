<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BedCount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class BedCountController extends Controller
{
    protected string $viewPath = 'backend.bed_counts.';
    protected string $routeName = 'admin.bed_counts.';

    // Display all bed counts
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = BedCount::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('number', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('number_fr', function ($row) {
                    $translation = $row->translations->where('language', 'fr')->first();
                    return $translation ? $translation->number : '<span class="text-muted">No Translation</span>';
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
                ->rawColumns(['number_fr', 'action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new bed count
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'number'    => 'required|string|max:10|unique:bed_counts,number',
            'number_fr' => 'nullable|string|max:10',
        ]);

        try {
            $bedCount = BedCount::create(['number' => $request->number]);
            $bedCount->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['number'   => $request->number_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Bed count added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add bed count.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        // with('translations') oboshoy add korun
        $bedCount = BedCount::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('bedCount'));
    }
    // Update bed count
    public function update(Request $request, $id): RedirectResponse
    {
        $bedCount = BedCount::findOrFail($id);

        $request->validate([
            'number'    => 'required|string|max:10|unique:bed_counts,number,' . $bedCount->id,
            'number_fr' => 'nullable|string|max:10',
        ]);

        try {
            $bedCount->update(['number' => $request->number]);
            $bedCount->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['number'   => $request->number_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Bed count updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update bed count.');
        }
    }

    // Delete bed count
    public function destroy($id): JsonResponse
    {
        $bedCount = BedCount::findOrFail($id);
        $bedCount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bed count deleted successfully!',
        ]);
    }
}
