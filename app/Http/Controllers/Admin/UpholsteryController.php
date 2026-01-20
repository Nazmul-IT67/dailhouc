<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upholstery;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class UpholsteryController extends Controller
{
    protected string $viewPath = 'backend.upholsteries.';
    protected string $routeName = 'admin.upholsteries.';

    // Display all upholsteries
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Upholstery::latest();

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

    // Store new upholstery
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:upholsteries,name',
                'name_fr' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $upholstery = Upholstery::create($request->only('name'));
            $upholstery->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Upholstery created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Upholstery creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $upholstery = Upholstery::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('upholstery'));
    }

    // Update upholstery
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $upholstery = Upholstery::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:100|unique:upholsteries,name,' . $upholstery->id,
                'name_fr' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $upholstery->update($request->only('name'));
            $upholstery->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['name'     => $request->name_fr]
            );

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Upholstery updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Upholstery update failed.');
        }
    }

    // Delete upholstery
    public function destroy($id): JsonResponse
    {
        $upholstery = Upholstery::findOrFail($id);
        $upholstery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Upholstery deleted successfully!',
        ]);
    }
}
