<?php

namespace App\Services;

use App\Models\{BodyColor, BodyType, BedType, Equipment, InteriorColor, Upholstery};
use App\Repositories\VehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function __construct(protected VehicleRepository $repo)
    {
    }

    public function search(Request $request): array
    {
        $vehicles = $this->repo->getFilteredVehicles($request);

        if ($vehicles->isEmpty()) {
            return ['filters' => [], 'vehicles' => []];
        }

        $lookups  = $this->resolveLookupNames($request);
        $filters  = $this->buildFilters($request, $vehicles, $lookups);

        $this->logSearchIfAuthenticated($request, $lookups);

        return compact('filters', 'vehicles');
    }

    // ─── Filter summary for response ────────────────────────────────────────────

    private function buildFilters(Request $request, LengthAwarePaginator $vehicles, array $lookups): array
    {
        $first = $vehicles->first();

        return array_filter([
            'category'         => $request->category_id ? optional($first?->category)->name : null,
            'brand'            => $request->brand_id ? optional($first?->brand)->name : null,
            'model'            => $request->model_id ? optional($first?->model)->name : null,
            'sub_model'        => $request->sub_model_id ? optional($first?->subModel)->name : null,
            'fuel'             => $request->fuel_id ? optional($first?->fuel)->title : null,
            'transmission'     => $request->transmission_id ? optional($first?->transmission)->title : null,
            'seller_type'      => $request->seller_type_id ? optional($first?->seller_type)->title : null,
            'previous_owner'   => $request->previous_owner_id
                ? optional($first?->data?->previousOwner)->number : null,
            'vehicle_conditions' => $request->vehicle_conditions_id
                ? optional($first?->data?->condition)->name : null,
            'engine_emission_class' => $request->emission_classes_id
                ? optional($first?->engineAndEnvironment?->emissionClass)->title : null,

            'lat'    => $request->lat    ?? null,
            'lng'    => $request->lng    ?? null,
            'radius' => $request->radius ?? null,

            'budget_from'  => $request->budget_from  ?? null,
            'budget_to'    => $request->budget_to    ?? null,
            'indicate_vat' => $request->filled('indicate_vat') ? (int) $request->indicate_vat : null,

            'first_registration_from' => $request->first_registration_from ?? null,
            'first_registration_to'   => $request->first_registration_to   ?? null,
            'mileage_from'            => $request->mileage_from ?? null,
            'mileage_to'              => $request->mileage_to   ?? null,
            'door_count_from'         => $request->door_count_from ?? null,
            'door_count_to'           => $request->door_count_to   ?? null,
            'number_of_seats_from'    => $request->number_of_seats_from ?? null,
            'number_of_seats_to'      => $request->number_of_seats_to   ?? null,

            'power_range' => $this->buildPowerRange($request),

            'body_type'    => $lookups['bodyTypeNames'] ?: null,
            'equipment'    => $lookups['equipmentNames'] ?: null,
            'body_color'   => $lookups['bodyColorNames'],
            'interior_color' => $lookups['interiorColorNames'],
            'upholstery'   => $lookups['upholsteryNames'],
            'bed_type'     => $lookups['bedTypeNames'] ?: null,

            'service_history'  => $request->filled('service_history') ? (int) $request->service_history : null,
            'damaged_vehicle'  => $request->filled('damaged_vehicle') ? (int) $request->damaged_vehicle : null,
            'guarantee'        => $request->filled('guarantee') ? (int) $request->guarantee : null,
            'technical_inspection_valid_until' => $request->filled('technical_inspection_valid_until')
                ? (int) $request->technical_inspection_valid_until : null,
            'metalic'          => $request->filled('metalic') ? (int) $request->metalic : null,
            'catalytic_converter' => $request->filled('catalytic_converter') ? $request->catalytic_converter : null,
            'particle_filter'  => $request->filled('particle_filter') ? $request->particle_filter : null,
            'axle_count'       => $request->filled('axle_count_id') ? $request->axle_count_id : null,
            'perm_gvw'         => $request->filled('perm_gvw') ? $request->perm_gvw : null,
        ], fn ($v) => $v !== null);
    }

    private function buildPowerRange(Request $request): ?array
    {
        if (!$request->filled('power_from') && !$request->filled('power_to') && !$request->filled('power_unit')) {
            return null;
        }

        return [
            'from' => $request->power_from ?? null,
            'to'   => $request->power_to   ?? null,
            'unit' => $request->power_unit ?? null,
        ];
    }

    // ─── Lookup names (for filter labels & search log) ───────────────────────────

    private function resolveLookupNames(Request $request): array
    {
        return [
            'bodyColorNames'    => $request->filled('body_color_id')
                ? BodyColor::whereIn('id', (array) $request->body_color_id)->pluck('name')->toArray() : [],
            'interiorColorNames' => $request->filled('interior_color_id')
                ? InteriorColor::whereIn('id', (array) $request->interior_color_id)->pluck('name')->toArray() : [],
            'upholsteryNames'   => $request->filled('upholstery_id')
                ? Upholstery::whereIn('id', (array) $request->upholstery_id)->pluck('name')->toArray() : [],
            'equipmentNames'    => $request->filled('equipment_ids')
                ? Equipment::whereIn('id', $request->equipment_ids)->pluck('title')->toArray() : [],
            'bedTypeNames'      => $request->filled('bed_type_id')
                ? BedType::whereIn('id', (array) $request->bed_type_id)->pluck('name')->toArray() : [],
            'bodyTypeNames'     => $request->filled('body_type_id')
                ? BodyType::whereIn('id', (array) $request->body_type_id)->pluck('title')->toArray() : [],
        ];
    }

    // ─── Search log ─────────────────────────────────────────────────────────────

    private function logSearchIfAuthenticated(Request $request, array $lookups): void
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return;
        }

        $logFilters = $this->buildLogFilters($request, $lookups);

        // TODO: persist $logFilters — e.g. SearchLog::create([...])
        // SearchLog::create(['user_id' => $user->id, 'filters' => $logFilters]);
    }

    private function buildLogFilters(Request $request, array $lookups): array
    {
        $raw = [
            'budget'       => ['from' => $request->budget_from, 'to' => $request->budget_to],
            'mileage'      => ['from' => $request->mileage_from, 'to' => $request->mileage_to],
            'registration' => ['from' => $request->first_registration_from, 'to' => $request->first_registration_to],
            'power'        => ['from' => $request->power_from, 'to' => $request->power_to, 'unit' => $request->power_unit],
            'category'     => $request->category_id
                ? ['id' => $request->category_id, 'name' => \App\Models\Category::find($request->category_id)?->name] : null,
            'brand'        => $request->brand_id
                ? ['id' => $request->brand_id, 'name' => \App\Models\Brand::find($request->brand_id)?->name] : null,
            'models'       => $request->filled('model_id')
                ? \App\Models\CarModel::whereIn('id', (array) $request->model_id)->get(['id', 'name'])->toArray() : null,
            'sub_models'   => $request->filled('sub_model_id')
                ? \App\Models\SubModel::whereIn('id', (array) $request->sub_model_id)->get(['id', 'name'])->toArray() : null,
            'fuel'         => $request->fuel_id
                ? ['id' => $request->fuel_id, 'name' => \App\Models\Fuel::find($request->fuel_id)?->title] : null,
            'transmission' => $request->transmission_id
                ? ['id' => $request->transmission_id, 'name' => \App\Models\Transmission::find($request->transmission_id)?->title] : null,
            'body_types'   => $request->filled('body_type_id')
                ? \App\Models\BodyType::whereIn('id', (array) $request->body_type_id)->get(['id', 'title as name'])->toArray() : null,
            'equipments'   => $request->filled('equipment_ids')
                ? \App\Models\Equipment::whereIn('id', (array) $request->equipment_ids)->get(['id', 'title as name'])->toArray() : null,
            'colors'       => ['body' => $lookups['bodyColorNames'], 'interior' => $lookups['interiorColorNames']],
            'location'     => $request->filled('lat')
                ? ['lat' => $request->lat, 'lng' => $request->lng, 'radius' => $request->radius] : null,
            'others'       => [
                'damaged_vehicle' => $request->damaged_vehicle,
                'service_history' => $request->service_history,
                'seller_type'     => $request->seller_type_id,
            ],
        ];

        return $this->removeEmpty($raw);
    }

    private function removeEmpty(array $array): array
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->removeEmpty($value);
            }
            if (is_null($value) || $value === '' || (is_array($value) && empty($value))) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
