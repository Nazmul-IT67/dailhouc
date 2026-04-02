<?php

namespace App\Http\Controllers\API\Vehicle;

use App\Models\Vehicle;
use App\Models\Currency;
use App\Models\SubModel;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Notifications\GeneralNotification;

class StoreVehicleController extends Controller
{
    use ApiResponse;

    public function store(StoreVehicleRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated. Please log in to access your vehicles.', 200);
        }

        $subModel = SubModel::where('id', $request->sub_model_id)
            ->where('car_model_id', $request->model_id)
            ->first();
        if (!$subModel) {
            return response()->json([
                'success' => false,
                'errors'  => ['sub_model_id' => ['Selected submodel does not belong to the chosen model.']]
            ], 422);
        }

        $baseCurrency = Currency::where('is_default', 1)->first();
        if (!$baseCurrency) {
            return $this->error([], 'Base currency is not set!', 500);
        }

        // Determine user's country currency (fallback to base)
        $userCountry = $user->country ?? null;
        $userCurrency = $userCountry?->currency ?? $baseCurrency;

        // Price from request is assumed to be in the user's local currency
        $priceInUserCurrency = $request->input('price', 0);

        // Convert to base currency (e.g., USD) using existing helper
        // convertPrice(amount, fromCode, toCode)
        $priceInBaseCurrency = convertPrice($priceInUserCurrency, $userCurrency->code, $baseCurrency->code);

        // try {
            $vehicle = Vehicle::create(array_merge(
                $request->only([
                    'category_id',
                    'brand_id',
                    'model_id',
                    'fuel_id',
                    'sub_model_id',
                    'first_registration',
                    'body_type_id',
                    'transmission_id',
                    'power_id',
                    'equipment_line_id',
                    'seller_type_id',
                    'milage',
                    'description',
                    'equipment_ids',
                    'engine_displacement'
                ]),
                [
                    'user_id' => $user->id,
                    'featured_request' => $request->boolean('featured_request', false),
                    'is_featured' => false,
                    'currency_id' => $userCurrency->id,
                    'price' => $priceInUserCurrency,
                    'price_in_base' => $priceInBaseCurrency,
                    'base_currency_id' => $baseCurrency->id,
                ]
            ));

            // Save related data dynamically
            $relations = [
                'data' => [
                    'vehicle_conditions_id',
                    'body_color_id',
                    'upholstery_id',
                    'interior_color_id',
                    'previous_owner_id',
                    'num_of_door_id',
                    'num_of_seats_id',
                    'metalic',
                    'negotiable',
                    'indicate_vat',
                    'bed_count_id',
                    'bed_type_id',
                ],
                'engineAndEnvironment' => [
                    'driver_type_id',
                    'transmission_id',
                    'num_of_gears_id',
                    'cylinders_id',
                    'emission_classes_id',
                    'axle_count_id',
                    'perm_gvw',
                    'engine_size',
                    'kerb_weight',
                    'fuel_consumption_urban',
                    'fuel_consumption_combined',
                    'fuel_consumption_combined_gm',
                    'co2_emissions',
                    'catalytic_converter',
                    'particle_filter'
                ],
                'conditionAndMaintenance' => [
                    'service_history',
                    'non_smoker_car',
                    'damaged_vehicle',
                    'guarantee',
                    'recent_change_of_timing_belt',
                    'recent_technical_service',
                    'technical_inspection_valid_until'
                ]
            ];
            foreach ($relations as $relation => $fields) {
                if ($relation === 'data' && $request->has('bed_type_id')) {
                    $data['bed_type_id'] = $request->bed_type_id; // array of IDs
                }

                $vehicle->$relation()->create($request->only($fields));
            }

            // Upload photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $vehicle->photos()->create([
                        'file_path' => uploadImage($photo, 'Vehicle'),
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            // Create contact info array
            $data = [
                'name'                  => $request->input('name', $user->name),
                'email'                 => $user->email,
                'phone'                 => $request->input('phone', $user->phone ?? "Null"),
                
                'whatsapp_number'       => $request->input('whatsapp_number', $user->whatsapp), 
                'whatsapp_country_code' => $request->input('whatsapp_country_code', $user->code), 
                
                'country_id'            => $request->input('country_id', $user->country_id),
                'city_id'               => $request->input('city_id', $user->city_id),
                'postal_code'           => $request->input('postal_code', $user->postal_code),
                'street_details'        => $request->input('street_details', $user->street_address ?? null), 
                
                'lat'                   => $request->input('lat', $user->lat),
                'lng'                   => $request->input('lng', $user->lng),
                
                'is_email_show'         => $request->has('is_email_show') ? $request->boolean('is_email_show') : true,
                'is_number_show'        => $request->has('is_number_show') ? $request->boolean('is_number_show') : true,
                'is_whatsapp_show'      => $request->has('is_whatsapp_show') ? $request->boolean('is_whatsapp_show') : true,
            ];

            if ($request->hasFile('avatar')) {
                $data['avatar'] = uploadImage($request->file('avatar'), 'User/Avatar');
            } elseif ($user->avatar) {
                $data['avatar'] = $user->avatar;
            }

            // Create contact info
            $vehicle->contactInfo()->create($data);
            $vehicle->load(['data', 'photos', 'engineAndEnvironment', 'conditionAndMaintenance', 'contactInfo']);

            $user->notify(new GeneralNotification(
                'Vehicle Added',
                'Your vehicle "' . ($vehicle->brand?->name ?? '') . ' ' . ($vehicle->model?->name ?? '') . '" has been added successfully.'
            ));

            return $this->success($vehicle, 'Vehicle added successfully!', 200);
        // } catch (\Exception $e) {
        //     \Log::error('Vehicle store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        //     return $this->error([], 'Something went wrong', 200);
        // }
    }

    public function show(Request $request)
    {
        $id = $request->id;

        if (!$id) {
            return $this->error([], 'Vehicle ID is required!', 400);
        }

        $vehicle = Vehicle::select(
                'vehicles.id', 'vehicles.category_id', 'vehicles.brand_id', 'vehicles.model_id',
                'vehicles.body_type_id', 'vehicles.sub_model_id', 'vehicles.power_id',
                'vehicles.price', 'vehicles.price_in_base', 'vehicles.currency_id',
                'vehicles.base_currency_id', 'vehicles.first_registration'
            )
            ->with([
                'power', 'category', 'brand', 'model', 'subModel', 
                'body_type', 'data.condition', 'photos', 'contactInfo', 
                'contactInfo.country', 'contactInfo.city', 'currency', 'baseCurrency'
            ])
            ->find($id);

        if (!$vehicle) {
            return $this->error([], 'Vehicle not found!', 404);
        }

        $baseCurrency = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        if (!$baseCurrency) {
            return $this->error([], 'Base currency is not set!', 500);
        }

        $user = auth()->user();
        $viewerCurrency = $user?->country?->currency ?? $baseCurrency;

        $vehicle->poster_price = number_format($vehicle->price, 2);
        $vehicle->poster_currency_symbol = $vehicle->currency?->symbol ?? '';
        $vehicle->poster_currency_code = $vehicle->currency?->code ?? '';

        $vehicle->base_price = number_format($vehicle->price_in_base, 2);
        $vehicle->base_currency_symbol = $vehicle->baseCurrency?->symbol ?? '$';
        $vehicle->base_currency_code = $vehicle->baseCurrency?->code ?? 'USD';

        $vehicle->viewer_price = number_format(
            convertPrice($vehicle->price_in_base, $baseCurrency->code, $viewerCurrency->code), 
            2
        );
        $vehicle->viewer_currency_symbol = $viewerCurrency->symbol;
        $vehicle->viewer_currency_code = $viewerCurrency->code;

        if ($vehicle->data) {
            $vehicle->data->bed_types = $vehicle->data->bedTypes;
        }

        return $this->success($vehicle, 'Vehicle details fetched successfully!', 200);
    }

    public function update(StoreVehicleRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated.', 401);
        }

        $vehicle = Vehicle::where('user_id', $user->id)->find($request->id);

        if (!$vehicle) {
            return $this->error([], 'Vehicle not found or you do not have permission to update it.', 404);
        }

        if ($request->has('sub_model_id') || $request->has('model_id')) {
            $modelId = $request->model_id ?? $vehicle->model_id;
            $subModelId = $request->sub_model_id ?? $vehicle->sub_model_id;

            $subModel = SubModel::where('id', $subModelId)
                ->where('car_model_id', $modelId)
                ->first();

            if (!$subModel) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['sub_model_id' => ['Selected submodel does not belong to the chosen model.']]
                ], 422);
            }
        }

        $baseCurrency = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $userCurrency = $user->country?->currency ?? $baseCurrency;

        if ($request->has('price')) {
            $priceInUserCurrency = $request->input('price');
            $priceInBaseCurrency = convertPrice($priceInUserCurrency, $userCurrency->code, $baseCurrency->code);
            
            $vehicle->price = $priceInUserCurrency;
            $vehicle->price_in_base = $priceInBaseCurrency;
        }

        \DB::beginTransaction();

        try {
            $vehicle->update($request->only([
                'category_id', 'brand_id', 'model_id', 'fuel_id', 'sub_model_id',
                'first_registration', 'body_type_id', 'transmission_id', 'power_id',
                'equipment_line_id', 'seller_type_id', 'milage', 'description',
                'equipment_ids', 'engine_displacement'
            ]));

            $relations = [
                'data' => [
                    'vehicle_conditions_id', 'body_color_id', 'upholstery_id', 'interior_color_id',
                    'previous_owner_id', 'num_of_door_id', 'num_of_seats_id', 'metalic',
                    'negotiable', 'indicate_vat', 'bed_count_id', 'bed_type_id'
                ],
                'engineAndEnvironment' => [
                    'driver_type_id', 'transmission_id', 'num_of_gears_id', 'cylinders_id',
                    'emission_classes_id', 'axle_count_id', 'perm_gvw', 'engine_size',
                    'kerb_weight', 'fuel_consumption_urban', 'fuel_consumption_combined',
                    'fuel_consumption_combined_gm', 'co2_emissions', 'catalytic_converter', 'particle_filter'
                ],
                'conditionAndMaintenance' => [
                    'service_history', 'non_smoker_car', 'damaged_vehicle', 'guarantee',
                    'recent_change_of_timing_belt', 'recent_technical_service', 'technical_inspection_valid_until'
                ]
            ];

            foreach ($relations as $relation => $fields) {
                if ($request->anyFilled($fields)) {
                    $relationData = $request->only($fields);
                    
                    if ($relation === 'data' && is_array($request->bed_type_id)) {
                        $relationData['bed_type_id'] = json_encode($request->bed_type_id);
                    }

                    $vehicle->$relation()->updateOrCreate([], $relationData);
                }
            }

            if ($request->hasFile('photos')) {
                
                foreach ($request->file('photos') as $photo) {
                    $vehicle->photos()->create([
                        'file_path' => uploadImage($photo, 'Vehicle'),
                        'is_primary' => false,
                    ]);
                }
            }

            $contactData = $request->only([
                'name', 'whatsapp_number', 'country_id', 'city_id', 'postal_code', 
                'street_details', 'lat', 'lng', 'whatsapp_country_code'
            ]);

            if ($request->has('is_email_show')) $contactData['is_email_show'] = $request->boolean('is_email_show');
            if ($request->has('is_number_show')) $contactData['is_number_show'] = $request->boolean('is_number_show');
            if ($request->has('is_whatsapp_show')) $contactData['is_whatsapp_show'] = $request->boolean('is_whatsapp_show');

            if ($request->hasFile('avatar')) {
                $contactData['avatar'] = uploadImage($request->file('avatar'), 'User/Avatar');
            }

            $vehicle->contactInfo()->updateOrCreate([], $contactData);

            \DB::commit();

            $vehicle->load(['data', 'photos', 'engineAndEnvironment', 'conditionAndMaintenance', 'contactInfo']);
            return $this->success($vehicle, 'Vehicle updated successfully!', 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Vehicle update error: ' . $e->getMessage());
            return $this->error([], 'Something went wrong while updating.', 500);
        }
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated.', 401);
        }

        $vehicle = Vehicle::where('user_id', $user->id)->find($request->id);
        if (!$vehicle) {
            return $this->error([], 'Vehicle not found or you do not have permission to delete it.', 404);
        }

        \DB::beginTransaction();

        try {
            if ($vehicle->photos) {
                foreach ($vehicle->photos as $photo) {
                    $photo->delete();
                }
            }

            $vehicle->data()->delete();
            $vehicle->engineAndEnvironment()->delete();
            $vehicle->conditionAndMaintenance()->delete();
            $vehicle->contactInfo()->delete();

            $vehicle->delete();

            \DB::commit();

            return $this->success([], 'Vehicle and its related data deleted successfully!', 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Vehicle delete error: ' . $e->getMessage());
            return $this->error([], 'Something went wrong while deleting the vehicle.', 500);
        }
    }
}
