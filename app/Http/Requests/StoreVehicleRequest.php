<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId    = $this->category_id;

        $isCar         = $categoryId == 1;
        $isMotorbike   = $categoryId == 2;
        $isCaravan     = $categoryId == 3;
        $isTransporter = $categoryId == 4;
        $isTrail       = $categoryId == 5;

        return [
            'category_id'        => 'required|exists:categories,id',
            'brand_id'           => 'required|exists:brands,id',
            'model_id'           => 'required|exists:car_models,id',
            'first_registration' => 'required|date',
            // 'fuel_id'            => 'required|exists:fuels,id',
            // 'transmission_id'    => 'required|exists:transmissions,id',
            // 'power_id'           => 'required|exists:powers,id',
            'seller_type_id'     => 'required|exists:seller_types,id',
            'price'              => 'required|numeric',
            'milage'             => 'required|numeric',
            
            'num_of_door_id'  => Rule::requiredIf($isCar || $isCaravan || $isTransporter),
            'num_of_seats_id' => Rule::requiredIf($isCar || $isCaravan || $isTransporter),
            'num_of_gears_id' => Rule::requiredIf(!$isTrail),

            'body_type_id' => [
                Rule::requiredIf($isCar || $isMotorbike),
                'nullable',
                'exists:body_types,id'
            ],

            'driver_type_id'    => 'nullable|exists:driver_types,id',
            'axle_count_id'     => 'nullable|exists:axle_counts,id',
            'sub_model_id'      => 'nullable|exists:sub_models,id',
            'body_color_id'     => 'nullable|exists:body_colors,id',
            'metalic'           => 'nullable|boolean',
            'upholstery_id'     => 'nullable|exists:upholsteries,id',
            'interior_color_id' => 'nullable|exists:interior_colors,id',
            'previous_owner_id' => 'nullable|exists:previous_owners,id',
            'fuel_id'           => 'nullable|exists:fuels,id',
            'transmission_id'   => 'nullable|exists:transmissions,id',
            'power_id'          => 'nullable|exists:powers,id',
            'negotiable'        => 'nullable|boolean',
            'indicate_vat'      => 'nullable|boolean',
            'engine_size'       => 'nullable|numeric',
            'kerb_weight'       => 'nullable|numeric',
            'perm_gvw'          => 'nullable',
            'description'       => 'nullable|string',
            'service_history'   => 'nullable|boolean',
            'non_smoker_car'    => 'nullable|boolean',
            'damaged_vehicle'   => 'nullable|boolean',
            'guarantee'         => 'nullable|boolean',
            'cylinders_id'      => 'nullable|exists:cylinders,id',
            'bed_type_id'       => 'nullable|array',
            'bed_type_id.*'     => 'integer|exists:bed_types,id',

            'emission_classes_id'   => 'nullable|exists:emission_classes,id',
            'vehicle_conditions_id' => 'nullable|exists:vehicle_conditions,id',

            'recent_change_of_timing_belt'     => 'nullable|date',
            'recent_technical_service'         => 'nullable|date',
            'technical_inspection_valid_until' => 'nullable|date',

            // Media & Contact
            'equipment_ids'   => 'nullable|array',
            'equipment_ids.*' => 'exists:equipment,id',
            'photos.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name'            => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'country_id'      => 'nullable|exists:countries,id',
            'city_id'         => 'nullable|exists:cities,id',
        ];
    }
}