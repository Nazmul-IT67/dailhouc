<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreviousOwner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Validator;

class PreviousOwnerController extends Controller
{
    protected string $viewPath = 'backend.previous_owners.';
    protected string $routeName = 'admin.previous_owners.';

    // Display all previous owners
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = PreviousOwner::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('number', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['action'])
                ->make();
        }

        return view($this->viewPath . 'index');
    }

    // Show create form
    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    // Store new previous owner
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|min:1|unique:previous_owners,number',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            PreviousOwner::create($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Previous Owner option created successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Previous Owner creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $owner = PreviousOwner::findOrFail($id);
        return view($this->viewPath . 'edit', compact('owner'));
    }

    // Update previous owner
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $owner = PreviousOwner::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'number' => 'required|integer|min:1|unique:previous_owners,number,' . $owner->id,
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $owner->update($request->only('number'));

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Previous Owner option updated successfully.');
        } catch (Exception $e) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Previous Owner update failed.');
        }
    }

    // Delete previous owner
    public function destroy($id): JsonResponse
    {
        $owner = PreviousOwner::findOrFail($id);
        $owner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Previous Owner option deleted successfully!',
        ]);
    }
}
