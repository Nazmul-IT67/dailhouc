<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Exception;

class CityController extends Controller
{
    // Display all cities
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = City::with('country')->latest();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('country', function ($data) {
                    return $data->country->name ?? '-';
                })
                ->addColumn('action', function ($data) {
                    return '
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="' . route('admin.cities.edit', ['id' => $data->id]) . '"
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
                ->rawColumns(['country', 'action'])
                ->make();
        }

        return view('backend.cities.index');
    }

    // Show create form
    public function create(): View
    {
        $countries = Country::all();
        return view('backend.cities.create', compact('countries'));
    }

    // Store new city
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:cities,name',
                'country_id' => 'required|exists:countries,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            City::create($request->only(['name', 'country_id']));

            return redirect()->route('admin.cities.index')
                ->with('success', 'City created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.cities.index')
                ->with('error', 'City creation failed.');
        }
    }

    // Show edit form
    public function edit($id): View
    {
        $city = City::findOrFail($id);
        $countries = Country::all();
        return view('backend.cities.edit', compact('city', 'countries'));
    }

    // Update city
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $city = City::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:cities,name,' . $city->id,
                'country_id' => 'required|exists:countries,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            $city->update($request->only(['name', 'country_id']));

            return redirect()->route('admin.cities.index')
                ->with('success', 'City updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.cities.index')
                ->with('error', 'City update failed.');
        }
    }

    // Delete city
    public function destroy($id): JsonResponse
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully!',
        ]);
    }
}
