<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class SellerTypeController extends Controller
{
    protected string $viewPath = 'backend.seller_types.';
    protected string $routeName = 'admin.seller_types.';

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = SellerType::latest();

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

    public function create(): View
    {
        return view($this->viewPath . 'create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'    => 'required|string|max:255|unique:seller_types,title',
            'title_fr' => 'required|string|max:100',
        ]);

        try {
            $seller = SellerType::create(['title' => $request->title]);
            $seller->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Seller Type added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add Seller Type.');
        }
    }

    public function edit($id): View
    {
        $sellerType = SellerType::with('translations')->findOrFail($id);
        return view($this->viewPath . 'edit', compact('sellerType'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $sellerType = SellerType::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:255|unique:seller_types,title,' . $sellerType->id,
            'title_fr' => 'required|string|max:100',
        ]);

        try {
            $sellerType->update(['title' => $request->title]);
            $sellerType->translations()->updateOrCreate(
                ['language' => 'fr'],
                ['title'    => $request->title_fr]
            );

            return redirect()->route($this->routeName . 'index')->with('success', 'Seller Type updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Seller Type.');
        }
    }

    public function destroy($id): JsonResponse
    {
        $sellerType = SellerType::findOrFail($id);
        $sellerType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seller Type deleted successfully!',
        ]);
    }
}
