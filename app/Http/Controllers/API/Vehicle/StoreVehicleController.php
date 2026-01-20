<?php

namespace App\Http\Controllers\API\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Models\Currency;
use App\Models\Vehicle;
use App\Models\SubModel;
use App\Notifications\GeneralNotification;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

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

        try {
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

            $data = [
                'name' => $request->name,
                'email' => $user->email,
                'phone' => $user->phone ?? "Null",
                'whatsapp_number' => $request->whatsapp_number,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'postal_code' => $request->postal_code,
                'street_details' => $request->street_details,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'is_email_show' => $request->has('is_email_show') ? $request->boolean('is_email_show') : true,
                'is_number_show' => $request->has('is_number_show') ? $request->boolean('is_number_show') : true,
                'is_whatsapp_show' => $request->has('is_whatsapp_show') ? $request->boolean('is_whatsapp_show') : true,
                'whatsapp_country_code' => $request->whatsapp_country_code ?? null,
            ];

            // Handle avatar separately
            if ($request->hasFile('avatar')) {
                $data['avatar'] = uploadImage($request->file('avatar'), 'User/Avatar');
            }

            // Create contact info
            $vehicle->contactInfo()->create($data);


            $vehicle->load(['data', 'photos', 'engineAndEnvironment', 'conditionAndMaintenance', 'contactInfo']);

            $user->notify(new GeneralNotification(
                'Vehicle Added',
                'Your vehicle "' . ($vehicle->brand?->name ?? '') . ' ' . ($vehicle->model?->name ?? '') . '" has been added successfully.'
            ));

            return $this->success($vehicle, 'Vehicle added successfully!', 200);
        } catch (\Exception $e) {
            // Optional: log the exception for debugging
            \Log::error('Vehicle store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->error([], 'Something went wrong', 200);
        }
    }

    // public function store(StoreVehicleRequest $request)
    // {

    //     $user = Auth::user();
    //     if (!$user) {
    //         return $this->error([], 'Unauthenticated. Please log in to access your vehicles.', 200);
    //     }

    //     // Check if the submodel belongs to the selected model
    //     $subModel = SubModel::where('id', $request->sub_model_id)
    //         ->where('car_model_id', $request->model_id)
    //         ->first();

    //     if (!$subModel) {
    //         return response()->json([
    //             'success' => false,
    //             'errors'  => ['sub_model_id' => ['Selected submodel does not belong to the chosen model.']]
    //         ], 422);
    //     }

    //     $baseCurrency = Currency::where('is_default', 1)->first();
    //     if (!$baseCurrency) {
    //         return $this->error([], 'Base currency is not set!', 500);
    //     }
    //     try {
    //         $vehicle = Vehicle::create(array_merge(
    //             $request->only([
    //                 'category_id',
    //                 'brand_id',
    //                 'model_id',
    //                 'fuel_id',
    //                 'sub_model_id',
    //                 'first_registration',
    //                 'body_type_id',
    //                 'transmission_id',
    //                 'power_id',
    //                 'equipment_line_id',
    //                 'seller_type_id',
    //                 'price',
    //                 'milage',
    //                 'description',
    //                 'equipment_ids'
    //             ]),
    //             [
    //                 'user_id' => $user->id,
    //                 'featured_request' => $request->boolean('featured_request', false), // <-- featured request added
    //                 'is_featured' => false,
    //                 'currency_id' => $baseCurrency->id,
    //             ],
    //         ));

    //         // Save related data dynamically
    //         $relations = [
    //             'data' => [
    //                 'vehicle_conditions_id',
    //                 'body_color_id',
    //                 'upholstery_id',
    //                 'interior_color_id',
    //                 'previous_owner_id',
    //                 'num_of_door_id',
    //                 'num_of_seats_id',
    //                 'metalic',
    //                 'negotiable',
    //                 'indicate_vat',
    //                 'bed_count_id',
    //                 'bed_type_id',
    //             ],
    //             'engineAndEnvironment' => [
    //                 'driver_type_id',
    //                 'transmission_id',
    //                 'num_of_gears_id',
    //                 'cylinders_id',
    //                 'emission_classes_id',
    //                 'axle_count_id',
    //                 'perm_gvw',
    //                 'engine_size',
    //                 'kerb_weight',
    //                 'fuel_consumption_urban',
    //                 'fuel_consumption_combined',
    //                 'fuel_consumption_combined_gm',
    //                 'co2_emissions',
    //                 'catalytic_converter',
    //                 'particle_filter'
    //             ],
    //             'conditionAndMaintenance' => [
    //                 'service_history',
    //                 'non_smoker_car',
    //                 'damaged_vehicle',
    //                 'guarantee',
    //                 'recent_change_of_timing_belt',
    //                 'recent_technical_service',
    //                 'technical_inspection_valid_until'
    //             ]
    //         ];
    //         foreach ($relations as $relation => $fields) {
    //             $vehicle->$relation()->create($request->only($fields));
    //         }

    //         // Upload photos
    //         if ($request->hasFile('photos')) {
    //             foreach ($request->file('photos') as $index => $photo) {
    //                 $vehicle->photos()->create([
    //                     'file_path' => uploadImage($photo, 'Vehicle'),
    //                     'is_primary' => $index === 0,
    //                 ]);
    //             }
    //         }

    //         $vehicle->contactInfo()->create([
    //             'name' => $request->name,
    //             'email' => $user->email, // fixed from authenticated user
    //             'phone' => $user->phone ?? "Null", // fixed from authenticated user
    //             'whatsapp_number' => $request->whatsapp_number,
    //             'country_id' => $request->country_id,
    //             'city_id' => $request->city_id,
    //             'postal_code' => $request->postal_code,
    //             'street_details' => $request->street_details,
    //             'lat' => $request->lat,
    //             'lng' => $request->lng,
    //         ]);

    //         $vehicle->load(['data', 'photos', 'engineAndEnvironment', 'conditionAndMaintenance', 'contactInfo']);

    //         $user->notify(new GeneralNotification(
    //             'Vehicle Added',
    //             'Your vehicle "' . ($vehicle->brand?->name ?? '') . ' ' . ($vehicle->model?->name ?? '') . '" has been added successfully.'
    //         ));
    //         return $this->success($vehicle, 'Vehicle added successfully!', 200);
    //     } catch (\Exception $e) {
    //         return $this->error([], 'Something went wrong', 200);
    //     }
    // }
}
