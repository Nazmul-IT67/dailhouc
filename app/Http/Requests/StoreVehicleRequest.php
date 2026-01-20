<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'category_id'        => 'required|exists:categories,id',
            'brand_id'           => 'required|exists:brands,id',
            'model_id'           => 'required|exists:car_models,id',
            'sub_model_id'       => 'required|exists:sub_models,id',
            'first_registration' => 'required|date',
            'body_type_id'       => 'required|exists:body_types,id',
            'fuel_id'            => 'nullable|exists:fuels,id',
            'transmission_id'    => 'nullable|exists:transmissions,id',
            'power_id'           => 'nullable|exists:powers,id',
            'equipment_line_id'  => 'nullable|exists:equipment_lines,id',
            'seller_type_id'     => 'required|exists:seller_types,id',
            'photos.*'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            'price'          => 'required|numeric',
            'milage'         => 'required|numeric',
            'description'    => 'required|string',
            'equipment_ids'  => 'required|array',
            'equipment_ids.*' => 'exists:equipment,id',

            // VehicleData
            'vehicle_conditions_id' => 'required|exists:vehicle_conditions,id',
            'body_color_id'         => 'required|exists:body_colors,id',
            'upholstery_id'         => 'nullable|exists:upholsteries,id',
            'interior_color_id'     => 'nullable|exists:interior_colors,id',
            'previous_owner_id'     => 'nullable|exists:previous_owners,id',
            'num_of_door_id'        => 'nullable|exists:number_of_doors,id',
            'num_of_seats_id'       => 'nullable|exists:number_of_seats,id',
            'metalic'               => 'nullable|boolean',
            'negotiable'            => 'nullable|boolean',
            'indicate_vat'          => 'nullable|boolean',
            'bed_type_id' => 'nullable|array',
            'bed_type_id.*' => 'integer|exists:bed_types,id',


            // Engine & Environment
            'driver_type_id'            => 'required|exists:driver_types,id',
            'transmission_id'           => 'required|exists:transmissions,id',
            'num_of_gears_id'           => 'required|exists:num_of_gears,id',
            'cylinders_id'              => 'required|exists:cylinders,id',
            'emission_classes_id'       => 'required|exists:emission_classes,id',
            'axle_count_id'             => 'nullable|exists:axle_counts,id',
            'perm_gvw'                  => 'nullable',

            'engine_size'               => 'nullable|numeric',
            'kerb_weight'               => 'nullable|numeric',
            'fuel_consumption_urban'    => 'nullable|numeric',
            'fuel_consumption_combined' => 'nullable|numeric',
            'fuel_consumption_combined_gm' => 'nullable|numeric',
            'co2_emissions'             => 'nullable|numeric',
            'catalytic_converter'       => 'nullable|boolean',
            'particle_filter'           => 'nullable|boolean',

            // Condition & Maintenance
            'service_history'                 => 'nullable|boolean',
            'non_smoker_car'                  => 'nullable|boolean',
            'damaged_vehicle'                 => 'nullable|boolean',
            'guarantee'                       => 'nullable|boolean',
            'recent_change_of_timing_belt'    => 'nullable|date',
            'recent_technical_service'        => 'nullable|date',
            'technical_inspection_valid_until' => 'nullable|date',

            'name' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'street_details' => 'nullable|string|max:255',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:20480',

            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ];
    }
}
