<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BedType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class BedTypeController extends Controller
{
    protected string $viewPath = 'backend.bed_types.';
    protected string $routeName = 'admin.bed_types.';

    // Display all bed types
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = BedType::latest();

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
                ->addColumn('action', function ($data) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
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
                ->rawColumns(['name_fr', 'action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new bed type
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:bed_types,name',
                'name_fr' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $bedType = BedType::create($request->only('name'));
            $bedType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Bed Type created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Bed Type creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $bedType = BedType::findOrFail($id);
        return view($this->viewPath . 'edit', compact('bedType'));
    }

    // Update bed type
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $bedType = BedType::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:bed_types,name,' . $bedType->id,
                'name_fr' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $bedType->update($request->only('name'));
            $bedType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Bed Type updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Bed Type update failed.');
        }
    }

    // Delete bed type
    public function destroy($id): JsonResponse
    {
        $bedType = BedType::findOrFail($id);
        $bedType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bed Type deleted successfully!',
        ]);
    }
}
