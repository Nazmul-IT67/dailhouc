<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class EquipmentController extends Controller
{
    protected string $viewPath = 'backend.equipment.';
    protected string $routeName = 'admin.equipment.';

    // Display all equipment
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Equipment::with('translations')->get();

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

    // Store new equipment
    public function store(Request $request): RedirectResponse
    {
        // ১. ভ্যালিডেশন-এ title_fr এড করুন
        $request->validate([
            'title'    => 'required|string|max:255|unique:equipment,title',
            'title_fr' => 'required|string|max:255', // French title field
        ]);

        try {
            // ২. মেইন ইকুইপমেন্ট ক্রিয়েট করা
            $equipment = Equipment::create([
                'title' => $request->title,
            ]);

            // ৩. ট্রানসলেশন টেবিলে ফ্রেঞ্চ ডেটা সেভ করা
            // আপনার মডেল-এ translations() রিলেশনশিপ থাকতে হবে
            $equipment->translations()->create([
                'language' => 'fr',
                'title'    => $request->title_fr,
            ]);

            return redirect()->route($this->routeName . 'index')
                ->with('success', 'Equipment added successfully with French translation!');

        } catch (Exception $e) {
            // Debugging-er jonno $e->getMessage() use korte paren
            return redirect()->back()
                ->with('error', 'Failed to add equipment: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $equipment = Equipment::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('equipment'));
    }

    // Update equipment
    public function update(Request $request, $id): RedirectResponse
    {
        $equipment = Equipment::findOrFail($id);

        $request->validate([
            'title_fr' => 'required|string|max:100',
            'title'    => 'required|string|max:255|unique:equipment,title,' . $equipment->id,
        ]);

        try {
            $equipment->update([
                'title' => $request->title,
            ]);

            $equipment->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Equipment updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update equipment.');
        }
    }

    // Delete equipment
    public function destroy($id): JsonResponse
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equipment deleted successfully!',
        ]);
    }
}
