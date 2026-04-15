<?php

namespace App\Repositories;

use App\Models\{
    Brand, CarModel, SubModel, BodyType, Fuel, Transmission,
    BodyColor, InteriorColor, Upholstery, Equipment, BedType,
    Category, Vehicle, SellerType
};
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FilterOptionRepository
{
    /**
     * Build a base Vehicle query with all currently selected filters applied.
     * This is the "context" — every option count runs against this base.
     */
    public function baseQuery(Request $request, array $exclude = []): Builder
    {
        $query = Vehicle::where('vehicles.status', 1);

        if (!in_array('category_id', $exclude) && $request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if (!in_array('brand_id', $exclude) && $request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if (!in_array('model_id', $exclude) && $request->filled('model_id')) {
            $query->whereIn('model_id', (array) $request->model_id);
        }
        if (!in_array('sub_model_id', $exclude) && $request->filled('sub_model_id')) {
            $query->whereIn('sub_model_id', (array) $request->sub_model_id);
        }
        if (!in_array('body_type_id', $exclude) && $request->filled('body_type_id')) {
            $query->whereIn('body_type_id', (array) $request->body_type_id);
        }
        if (!in_array('fuel_id', $exclude) && $request->filled('fuel_id')) {
            $query->whereIn('fuel_id', (array) $request->fuel_id);
        }
        if (!in_array('transmission_id', $exclude) && $request->filled('transmission_id')) {
            $query->whereIn('transmission_id', (array) $request->transmission_id);
        }
        if (!in_array('body_color_id', $exclude) && $request->filled('body_color_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('body_color_id', (array) $request->body_color_id));
        }
        if (!in_array('interior_color_id', $exclude) && $request->filled('interior_color_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('interior_color_id', (array) $request->interior_color_id));
        }
        if (!in_array('upholstery_id', $exclude) && $request->filled('upholstery_id')) {
            $query->whereHas('data', fn ($q) => $q->whereIn('upholstery_id', (array) $request->upholstery_id));
        }
        if (!in_array('seller_type_id', $exclude) && $request->filled('seller_type_id')) {
            $query->whereIn('seller_type_id', (array) $request->seller_type_id);
        }

        if (!in_array('bed_type_id', $exclude) && $request->filled('bed_type_id')) {
            $query->whereHas('data', function($q) use ($request) {
                $q->where(function($inner) use ($request) {
                    foreach ((array) $request->bed_type_id as $id) {
                        $inner->orWhereRaw('JSON_CONTAINS(bed_type_id, ?)', [json_encode((int)$id)]);
                    }
                });
            });
        }

        if (!in_array('equipment_ids', $exclude) && $request->filled('equipment_ids')) {
            $query->where(function($q) use ($request) {
                foreach ((array) $request->equipment_ids as $id) {
                    $q->orWhereJsonContains('vehicles.equipment_ids', (int)$id);
                }
            });
        }

        return $query;
    }

    // ─── Each filter option with count ──────────────────────────────────────────

    public function getTotalCount(Request $request): int
    {
        return $this->baseQuery($request)->count();
    }

    /**
     * Categories — no dependency, always full count
     */
    public function getCategories(Request $request): array
    {
        $counts = $this->baseQuery($request, ['category_id'])
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return Category::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => (int) $request->category_id === $item->id,
            ])->toArray();
    }

    /**
     * Brands — depends on: category
     */
    public function getBrands(Request $request): array
    {
        $counts = $this->baseQuery($request, ['brand_id'])
            ->selectRaw('brand_id, count(*) as total')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        return Brand::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->brand_id),
            ])->toArray();
    }

    /**
     * Models — depends on: category + brand
     */
    public function getModels(Request $request): array
    {
        $counts = $this->baseQuery($request, ['model_id'])
            ->selectRaw('model_id, count(*) as total')
            ->groupBy('model_id')
            ->pluck('total', 'model_id');

        return CarModel::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->model_id),
            ])->toArray();
    }

    /**
     * Sub Models — depends on: category + brand + model
     */
    public function getSubModels(Request $request): array
    {
        $counts = $this->baseQuery($request, ['sub_model_id'])
            ->selectRaw('sub_model_id, count(*) as total')
            ->groupBy('sub_model_id')
            ->pluck('total', 'sub_model_id');

        return SubModel::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->sub_model_id),
            ])->toArray();
    }

    /**
     * Body Types — depends on: category + brand + model + sub_model
     */
    public function getBodyTypes(Request $request): array
    {
        $counts = $this->baseQuery($request, ['body_type_id'])
            ->selectRaw('body_type_id, count(*) as total')
            ->groupBy('body_type_id')
            ->pluck('total', 'body_type_id');

        return BodyType::orderBy('title')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->title,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->body_type_id),
            ])->toArray();
    }

    /**
     * Fuels — depends on: category + brand + model + sub_model + body_type
     */
    public function getFuels(Request $request): array
    {
        $counts = $this->baseQuery($request, ['fuel_id'])
            ->selectRaw('fuel_id, count(*) as total')
            ->groupBy('fuel_id')
            ->pluck('total', 'fuel_id');

        return Fuel::orderBy('title')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->title,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->fuel_id),
            ])->toArray();
    }

    /**
     * Transmissions — depends on: category + brand + model + sub_model + body_type + fuel
     */
    public function getTransmissions(Request $request): array
    {
        $counts = $this->baseQuery($request, ['transmission_id'])
            ->selectRaw('transmission_id, count(*) as total')
            ->groupBy('transmission_id')
            ->pluck('total', 'transmission_id');

        return Transmission::orderBy('title')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->title,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->transmission_id),
            ])->toArray();
    }

    /**
     * Body Colors — depends on: all above filters
     */
    public function getBodyColors(Request $request): array
    {
        $counts = $this->baseQuery($request, ['body_color_id'])
            ->join('vehicle_data', 'vehicle_data.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicle_data.body_color_id, count(*) as total')
            ->groupBy('vehicle_data.body_color_id')
            ->pluck('total', 'body_color_id');

        return BodyColor::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->body_color_id),
            ])->toArray();
    }

    /**
     * Interior Colors — depends on: all above filters
     */
    public function getInteriorColors(Request $request): array
    {
        $counts = $this->baseQuery($request, ['interior_color_id'])
            ->join('vehicle_data', 'vehicle_data.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicle_data.interior_color_id, count(*) as total')
            ->groupBy('vehicle_data.interior_color_id')
            ->pluck('total', 'interior_color_id');

        return InteriorColor::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->interior_color_id),
            ])->toArray();
    }

    /**
     * Bed Type — depends on: all above filters
     */
    public function getBedType(Request $request): array
    {
        $counts = $this->baseQuery($request, ['bed_type_id'])
            ->join('vehicle_data', 'vehicle_data.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicle_data.bed_type_id as bed_type_id, count(*) as total')
            ->whereNotNull('vehicle_data.bed_type_id')
            ->groupBy('vehicle_data.bed_type_id')
            ->get();

        return BedType::orderBy('name')
            ->get()
            ->map(function ($item) use ($counts, $request) {
                return [
                    'id'       => $item->id,
                    'name'     => $item->name,
                    'count'    => $this->parseBedTypeJsonCount($counts, $item->id),
                    'selected' => in_array($item->id, (array) $request->bed_type_id),
                ];
            })->toArray();
    }

    /**
     * Bed Type JSON
     */
    private function parseBedTypeJsonCount($counts, $id)
    {
        $total = 0;
        foreach ($counts as $row) {
            $bedTypeIds = $row->bed_type_id;
            if (is_string($bedTypeIds)) {
                $bedTypeIds = json_decode($bedTypeIds, true);
            }

            if (is_array($bedTypeIds)) {
                if (in_array((string)$id, array_map('strval', $bedTypeIds))) {
                    $total += $row->total;
                }
            }
        }
        return $total;
    }

    /**
     * Interior Colors — depends on: all above filters
     */
    public function getUpholstery(Request $request): array
    {
        $counts = $this->baseQuery($request, ['upholstery_id'])
            ->join('vehicle_data', 'vehicle_data.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicle_data.upholstery_id, count(*) as total')
            ->groupBy('vehicle_data.upholstery_id')
            ->pluck('total', 'upholstery_id');

        return InteriorColor::orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->name,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->upholstery_id),
            ])->toArray();
    }

    /**
     * Interior Colors — depends on: all above filters
     */
    public function getSeller(Request $request): array
    {
        $counts = $this->baseQuery($request, ['seller_type_id'])
            ->selectRaw('seller_type_id, count(*) as total')
            ->groupBy('seller_type_id')
            ->pluck('total', 'seller_type_id');

        return SellerType::orderBy('title')
            ->get()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'name'     => $item->title,
                'count'    => $counts->get($item->id, 0),
                'selected' => in_array($item->id, (array) $request->seller_type_id),
            ])->toArray();
    }

    /**
     * Equipment — depends on: all above filters
     */
    public function getEquipment(Request $request): array
    {
        $counts = $this->baseQuery($request, ['equipment_ids'])
            ->selectRaw('vehicles.equipment_ids, count(*) as total')
            ->whereNotNull('vehicles.equipment_ids')
            ->groupBy('vehicles.equipment_ids')
            ->get();

        return Equipment::orderBy('title')
            ->get()
            ->map(function ($item) use ($counts, $request) {
                return [
                    'id'       => $item->id,
                    'name'     => $item->title,
                    'count'    => $this->parseJsonCount($counts, $item->id),
                    'selected' => in_array($item->id, (array) $request->equipment_ids),
                ];
            })->toArray();
    }

    private function parseJsonCount($counts, $id)
    {
        $total = 0;
        foreach ($counts as $row) {
            $equipmentIds = $row->equipment_ids;

            if (!is_array($equipmentIds)) {
                $equipmentIds = json_decode($equipmentIds, true);
            }

            if (is_array($equipmentIds)) {
                if (in_array((string)$id, array_map('strval', $equipmentIds))) {
                    $total += $row->total;
                }
            }
        }
        return $total;
    }
}
