<?php

namespace App\Http\Controllers\Admin\Engine_And_Environment;

use App\Http\Controllers\Controller;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class TransmissionController extends Controller
{
    protected string $viewPath = 'backend.engine_and_environment.transmissions.';
    protected string $routeName = 'admin.transmissions.';

    // Display all transmissions
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Transmission::latest();

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

    // Store new transmission
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'    => 'required|string|max:100|unique:transmissions,title',
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $transmission = Transmission::create(['title' => $request->title]);
            $transmission->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Transmission added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add transmission.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $transmission = Transmission::findOrFail($id);
        return view($this->viewPath . 'edit', compact('transmission'));
    }

    // Update transmission
    public function update(Request $request, $id): RedirectResponse
    {
        $transmission = Transmission::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:100|unique:transmissions,title,' . $transmission->id,
            'title_fr' => 'nullable|string|max:100',
        ]);

        try {
            $transmission->update(['title' => $request->title]);
            $transmission->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'     => $request->title_fr]
            );
            return redirect()->route($this->routeName . 'index')->with('success', 'Transmission updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update transmission.');
        }
    }

    // Delete transmission
    public function destroy($id): JsonResponse
    {
        $transmission = Transmission::findOrFail($id);
        $transmission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transmission deleted successfully!',
        ]);
    }
}
