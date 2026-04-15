<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleRepository
{
    public function getFilteredVehicles(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Vehicle::where('vehicles.status', 1);

        $this->applyBasicFilters($query, $request);
        $this->applyRangeFilters($query, $request);
        $this->applyRelationFilters($query, $request);
        $this->applyColorFilters($query, $request);
        $this->applyEngineFilters($query, $request);
        $this->applyLocationFilter($query, $request);
        $this->applySorting($query, $request);

        $query->select(
            'vehicles.id',
            'vehicles.category_id',
            'vehicles.brand_id',
            'vehicles.user_id',
            'vehicles.model_id',
            'vehicles.body_type_id',
            'vehicles.sub_model_id',
            'vehicles.power_id',
            'vehicles.price',
            'vehicles.milage',
            'vehicles.engine_displacement',
            'vehicles.first_registration',
            'vehicles.fuel_id',
            'vehicles.transmission_id'
        )->with([
            'category',
            'brand'     => fn ($q) => $q->withCount(['vehicles' => fn ($q2) => $this->scopeActiveWithCategory($q2, $request)]),
            'model'     => fn ($q) => $q->withCount(['vehicles' => fn ($q2) => $this->scopeActiveWithCategory($q2, $request)]),
            'subModel'  => fn ($q) => $q->withCount(['vehicles' => fn ($q2) => $this->scopeActiveWithCategory($q2, $request)]),
            'body_type' => fn ($q) => $q->withCount(['vehicles' => fn ($q2) => $this->scopeActiveWithCategory($q2, $request)]),
            'photos', 'contactInfo.country', 'contactInfo.city',
            'power', 'data.condition', 'transmission', 'fuel',
        ]);

        return $query->paginate(20);
    }

    public function countFiltered(Request $request): int
    {
        $query = Vehicle::where('vehicles.status', 1);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return $query->count();
    }

    // ─── Private helpers ────────────────────────────────────────────────────────

    private function scopeActiveWithCategory($query, Request $request): void
    {
        $query->where('status', 1);
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    }

    private function applyBasicFilters($query, Request $request): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('model_id')) {
            $query->whereIn('model_id', (array) $request->model_id);
        }
        if ($request->filled('sub_model_id')) {
            $query->whereIn('sub_model_id', (array) $request->sub_model_id);
        }
        if ($request->filled('body_type_id')) {
            $query->whereIn('body_type_id', (array) $request->body_type_id);
        }
        if ($request->filled('fuel_id')) {
            $query->whereIn('fuel_id', (array) $request->fuel_id);
        }
        if ($request->filled('transmission_id')) {
            $query->whereIn('transmission_id', (array) $request->transmission_id);
        }
        if ($request->filled('seller_type_id')) {
            $query->where('seller_type_id', $request->seller_type_id);
        }

        if ($request->filled('add_with_pictures') && $request->add_with_pictures == 1) {
            $query->whereHas('photos');
        }

        if ($request->filled('posting_age') && is_numeric($request->posting_age)) {
            $query->whereDate('vehicles.created_at', '>=', now()->subDays($request->posting_age));
        }

        $equipmentIds = $request->input('equipment_ids', []);
        if (!empty($equipmentIds)) {
            $query->where(function ($q) use ($equipmentIds) {
                foreach ($equipmentIds as $id) {
                    $q->orWhereJsonContains('equipment_ids', $id);
                }
            });
        }
    }

    private function applyRangeFilters($query, Request $request): void
    {
        $this->applyRange($query, 'price', $request->budget_from, $request->budget_to);
        $this->applyRange($query, 'milage', $request->mileage_from, $request->mileage_to);
        $this->applyRange($query, 'engine_displacement', $request->displacement_from, $request->displacement_to);
        $this->applyRange($query, 'first_registration', $request->first_registration_from, $request->first_registration_to);
    }

    private function applyRelationFilters($query, Request $request): void
    {
        if ($request->filled('indicate_vat')) {
            $query->whereHas('data', fn ($q) => $q->where('indicate_vat', $request->indicate_vat));
        }
        if ($request->filled('vehicle_conditions_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('vehicle_conditions_id', (array) $request->vehicle_conditions_id));
        }
        if ($request->filled('previous_owner_id')) {
            $query->whereHas('data', fn ($q) => $q->where('previous_owner_id', $request->previous_owner_id));
        }
        if ($request->filled('bed_type_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('bed_type_id', (array) $request->bed_type_id));
        }
        if ($request->filled('metalic')) {
            $query->whereHas('data', fn ($q) => $q->where('metalic', (int) $request->metalic));
        }

        if ($request->filled('door_count_from') || $request->filled('door_count_to')) {
            $query->whereHas('data.numOfDoor', fn ($q) => $this->applyRange($q, 'number', $request->door_count_from, $request->door_count_to));
        }
        if ($request->filled('number_of_seats_from') || $request->filled('number_of_seats_to')) {
            $query->whereHas('data.numOfSeats', fn ($q) => $this->applyRange($q, 'number', $request->number_of_seats_from, $request->number_of_seats_to));
        }
        if ($request->filled('bed_count_from') || $request->filled('bed_count_to')) {
            $query->whereHas('data.bedCount', fn ($q) => $this->applyRange($q, 'number', $request->bed_count_from, $request->bed_count_to));
        }

        if ($request->filled('service_history')) {
            $query->whereHas('conditionAndMaintenance', fn ($q) => $q->where('service_history', $request->service_history));
        }
        if ($request->filled('damaged_vehicle')) {
            $query->whereHas('conditionAndMaintenance', fn ($q) => $q->where('damaged_vehicle', $request->damaged_vehicle));
        }
        if ($request->filled('guarantee')) {
            $query->whereHas('conditionAndMaintenance', fn ($q) => $q->where('guarantee', $request->guarantee));
        }
        if ($request->filled('technical_inspection_valid_until')) {
            $query->whereHas('conditionAndMaintenance', function ($q) use ($request) {
                $request->technical_inspection_valid_until == 1
                    ? $q->whereNotNull('technical_inspection_valid_until')
                    : $q->whereNull('technical_inspection_valid_until');
            });
        }

        // Power filter
        $hasPower = $request->filled('power_from') || $request->filled('power_to') || $request->filled('power_unit');
        if ($hasPower) {
            $query->whereHas('power', function ($q) use ($request) {
                $this->applyRange($q, 'value', $request->power_from, $request->power_to);
                if ($request->filled('power_unit')) {
                    $q->where('unit', $request->power_unit);
                }
            });
        }
    }

    private function applyColorFilters($query, Request $request): void
    {
        if ($request->filled('body_color_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('body_color_id', (array) $request->body_color_id));
        }
        if ($request->filled('interior_color_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('interior_color_id', (array) $request->interior_color_id));
        }
        if ($request->filled('upholstery_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('upholstery_id', (array) $request->upholstery_id));
        }
    }

    private function applyEngineFilters($query, Request $request): void
    {
        if ($request->filled('emission_classes_id')) {
            $query->whereHas('engineAndEnvironment', fn ($q) => $q->where('emission_classes_id', $request->emission_classes_id));
        }
        if ($request->filled('catalytic_converter')) {
            $query->whereHas('engineAndEnvironment', fn ($q) => $q->where('catalytic_converter', (int) $request->catalytic_converter));
        }
        if ($request->filled('particle_filter')) {
            $query->whereHas('engineAndEnvironment', fn ($q) => $q->where('particle_filter', (int) $request->particle_filter));
        }
        if ($request->filled('axle_count_id')) {
            $query->whereHas('engineAndEnvironment', fn ($q) => $q->where('axle_count_id', $request->axle_count_id));
        }
        if ($request->filled('perm_gvw')) {
            $query->whereHas('engineAndEnvironment', fn ($q) => $q->where('perm_gvw', 'like', "%{$request->perm_gvw}%"));
        }
    }

    private function applyLocationFilter($query, Request $request): void
    {
        if (!$request->filled('lat') || !$request->filled('lng')) {
            return;
        }

        $lat    = $request->lat;
        $lng    = $request->lng;
        $radius = $request->input('radius', optional(\App\Models\SystemSetting::first())->radius);

        $query->join('contact_infos', 'contact_infos.vehicle_id', '=', 'vehicles.id')
            ->selectRaw("(6371 * acos(
                cos(radians(?)) * cos(radians(contact_infos.lat)) *
                cos(radians(contact_infos.lng) - radians(?)) +
                sin(radians(?)) * sin(radians(contact_infos.lat))
            )) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    private function applySorting($query, Request $request): void
    {
        $sortMap = [
            'price_asc'            => ['vehicles.price',            'asc'],
            'price_desc'           => ['vehicles.price',            'desc'],
            'newest'               => ['vehicles.created_at',       'desc'],
            'registration_newest'  => ['vehicles.first_registration','desc'],
            'registration_oldest'  => ['vehicles.first_registration','asc'],
            'mileage_asc'          => ['vehicles.milage',           'asc'],
            'mileage_desc'         => ['vehicles.milage',           'desc'],
        ];

        if (isset($sortMap[$request->sort_by])) {
            [$col, $dir] = $sortMap[$request->sort_by];
            $query->orderBy($col, $dir);
            return;
        }

        if (in_array($request->sort_by, ['power_asc', 'power_desc'])) {
            $dir = $request->sort_by === 'power_asc' ? 'asc' : 'desc';
            $query->join('powers', 'vehicles.power_id', '=', 'powers.id')
                  ->select('vehicles.*')
                  ->orderBy('powers.value', $dir);
            return;
        }

        $query->latest('vehicles.created_at');
    }

    // Generic range helper
    private function applyRange($query, string $column, $from, $to): void
    {
        if ($from !== null && $to !== null) {
            $query->whereBetween($column, [$from, $to]);
        } elseif ($from !== null) {
            $query->where($column, '>=', $from);
        } elseif ($to !== null) {
            $query->where($column, '<=', $to);
        }
    }
}
