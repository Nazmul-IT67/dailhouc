<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Power;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class PowerController extends Controller
{
    protected string $viewPath = 'backend.powers.';
    protected string $routeName = 'admin.powers.';

    // Display all powers
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Power::latest();

            if ($request->input('search.value')) {
                $searchTerm = $request->input('search.value');
                $data->where('value', 'LIKE', "%$searchTerm%")
                    ->orWhere('unit', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
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

    // Store power
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        try {
            $kwInput = (float) $request->value; 
            $multiplier = 1.341;

            Power::create([
                'value'    => $request->value,
                'power_hp' => $kwInput * $multiplier,
            ]);

            return redirect()->route($this->routeName . 'index')->with('success', 'Power added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Power.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $power = Power::findOrFail($id);
        return view($this->viewPath . 'edit', compact('power'));
    }

    // Update power
    public function update(Request $request, $id): RedirectResponse
    {
        $power = Power::findOrFail($id);

        $request->validate([
            'value'    => 'required|string|max:255|unique:powers,value,' . $power->id,
        ]);

        try {
            $kwInput = (float) $request->value; 
            $multiplier = 1.341;

            $power->update([
                'value'    => $request->value,
                'power_hp' => $kwInput * $multiplier,
            ]);

            return redirect()->route($this->routeName . 'index')->with('success', 'Power updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Power.');
        }
    }

    // Delete power
    public function destroy($id): JsonResponse
    {
        $power = Power::findOrFail($id);
        $power->delete();

        return response()->json([
            'success' => true,
            'message' => 'Power deleted successfully!',
        ]);
    }
}
