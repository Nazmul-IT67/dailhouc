<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class CurrencyController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = Currency::latest();

            // Search filter
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%")
                    ->orWhere('code', 'LIKE', "%$searchTerm%")
                    ->orWhere('symbol', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()

                // Display symbol
                ->addColumn('symbol', function ($data) {
                    return '<span>' . e($data->symbol) . '</span>';
                })

                // Display exchange rate
                ->addColumn('exchange_rate', function ($data) {
                    return '<span>' . number_format($data->exchange_rate, 2) . '</span>';
                })
                ->addColumn('default', function ($data) {
                    return $data->is_default ? '<span class="badge bg-success">Default</span>' : '';
                })


                // Status toggle
                ->addColumn('status', function ($data) {
                    $checked = $data->status == "active" ? "checked" : "";
                    return '
                    <div class="form-check form-switch d-flex">
                        <input onclick="showStatusChangeAlert(' . $data->id . ')"
                               type="checkbox"
                               class="form-check-input status-toggle"
                               id="switch' . $data->id . '"
                               data-id="' . $data->id . '"
                               name="status" ' . $checked . '>
                        <label class="form-check-label ms-2" for="switch' . $data->id . '"></label>
                    </div>
                ';
                })

                // Action buttons
                ->addColumn('action', function ($data) {
                    return '
                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <a href="' . route('admin.currencies.edit', ['id' => $data->id]) . '"
                           type="button" class="text-white btn btn-primary" title="Edit">
                           <i class="fa fa-pencil" aria-hidden="true"></i>
                        </a>
                        <a href="#" onclick="showDeleteConfirm(' . $data->id . ')"
                           type="button" class="text-white btn btn-danger" title="Delete">
                           <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['symbol', 'exchange_rate', 'status', 'default', 'action'])
                ->make();
        }

        return view('backend.currency.index');
    }
    public function create(): View
    {
        return view('backend.currency.create');
    }

    public function store(Request $request)
    {
        try {


            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10|unique:currencies,code',
                'name' => 'required|string|max:100',
                'symbol' => 'required|string|max:10',
                'exchange_rate' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                // Pass validation messages to session for SweetAlert
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }
            $isDefault = $request->has('is_default');

            if ($isDefault) {
                // Remove default from other currencies
                Currency::where('is_default', true)->update(['is_default' => false]);
            }
            $currency = new Currency();
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->is_default = $isDefault;
            $currency->exchange_rate = $request->exchange_rate;
            $currency->save();


            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.currencies.index')
                ->with('error', 'Currency creation failed.');
        }
    }

    public function edit($id): View
    {
        $currency = Currency::findOrFail($id);
        return view('backend.currency.edit', compact('currency'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $currency = Currency::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
                'name' => 'required|string|max:100',
                'symbol' => 'required|string|max:10',
                'exchange_rate' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->with('t-validation', $validator->errors()->all())
                    ->withInput();
            }

            // Check if "Set as Default" is checked
            $isDefault = $request->has('is_default');

            if ($isDefault) {
                // Remove default from other currencies
                Currency::where('is_default', true)
                    ->where('id', '!=', $currency->id)
                    ->update(['is_default' => false]);
            }

            // Update this currency
            $currency->update(array_merge(
                $request->only(['code', 'name', 'symbol', 'exchange_rate']),
                ['is_default' => $isDefault]
            ));

            return redirect()->route('admin.currencies.index')
                ->with('success', 'Currency updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.currencies.index')
                ->with('error', 'Currency update failed.');
        }
    }



    public function destroy($id): JsonResponse
    {
        $currency = Currency::findOrFail($id);
        $currency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Currency deleted successfully!',
        ]);
    }
}
