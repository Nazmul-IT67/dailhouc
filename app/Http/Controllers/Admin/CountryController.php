<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class CountryController extends Controller
{
    // Display all countries
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = Country::with('currency')->latest();

            // Search filter
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%")
                    ->orWhere('code', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()

                // Display currency
                ->addColumn('currency', function ($data) {
                    return $data->currency->name ?? '-';
                })

                // Action buttons
                ->addColumn('action', function ($data) {
                    return '
                <div class="btn-group btn-group-sm" role="group">
                    <a href="' . route('admin.countries.edit', ['id' => $data->id]) . '"
                       class="text-white btn btn-primary" title="Edit">
                       <i class="fa fa-pencil"></i>
                    </a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')"
                       class="text-white btn btn-danger" title="Delete">
                       <i class="fa fa-trash-o"></i>
                    </a>
                </div>
                ';
                })
                ->rawColumns(['currency', 'action'])
                ->make();
        }

        return view('backend.countries.index');
    }


    // Show create form
    public function create()
    {
        $currencies = Currency::all();
        return view('backend.countries.create', compact('currencies'));
    }

    // Store new country
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:countries,name',
                'code' => 'required|string|max:10|unique:countries,code',
                'currency_id' => 'required|exists:currencies,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            Country::create($request->only(['name', 'code', 'currency_id']));

            return redirect()->route('admin.countries.index')
                ->with('success', 'Country created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.countries.index')
                ->with('error', 'Country creation failed.');
        }
    }

    // Show edit form
    public function edit($id)
    {
        $country = Country::findOrFail($id);
        $currencies = Currency::all();
        return view('backend.countries.edit', compact('country', 'currencies'));
    }

    // Update country
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $country = Country::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:countries,name,' . $country->id,
                'code' => 'required|string|max:10|unique:countries,code,' . $country->id,
                'currency_id' => 'required|exists:currencies,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $country->update($request->only(['name', 'code', 'currency_id']));

            return redirect()->route('admin.countries.index')
                ->with('t-success', 'Country updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.countries.index')
                ->with('error', 'Country update failed.');
        }
    }

    // Delete country

    public function destroy($id): JsonResponse
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully!',
        ]);
    }
}
