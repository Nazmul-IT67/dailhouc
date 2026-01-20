<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BodyColor;
use App\Models\Equipment;
use App\Models\InteriorColor;
use App\Models\SearchLog;
use App\Models\SystemSetting;
use App\Models\Upholstery;
use App\Models\UserSearchLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        // $query = Vehicle::with(['brand', 'category', 'model', 'subModel', 'data', 'contactInfo', 'photos', 'engineAndEnvironment', 'conditionAndMaintenance']);
        $query = Vehicle::select(
            'vehicles.id',
            'vehicles.category_id',
            'vehicles.brand_id',
            'vehicles.model_id',
            'vehicles.body_type_id',
            'vehicles.sub_model_id',
            'vehicles.power_id',
            'vehicles.price',
            'vehicles.milage',
            'vehicles.engine_displacement',
            'vehicles.first_registration'
        )->with(['category', 'brand', 'model', 'subModel', 'body_type', 'photos', 'contactInfo', 'power', 'data.condition']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('model_id')) {
            $modelIds = (array) $request->model_id;
            $query->whereIn('model_id', $modelIds);
        }
        if ($request->filled('sub_model_id')) {
            $subModelIds = (array) $request->sub_model_id;
            $query->whereIn('sub_model_id', $subModelIds);
        }

        if ($request->filled('budget_from') && $request->filled('budget_to')) {
            $query->whereBetween('price', [$request->budget_from, $request->budget_to]);
        } elseif ($request->filled('budget_from')) {
            $query->where('price', '>=', $request->budget_from);
        } elseif ($request->filled('budget_to')) {
            $query->where('price', '<=', $request->budget_to);
        }
        if ($request->filled('indicate_vat')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->where('indicate_vat', $request->indicate_vat);
            });
        }
        if ($request->filled('vehicle_conditions_id')) {
            $vehicleConditionIds = (array) $request->vehicle_conditions_id; // force array
            $query->whereHas('data', function ($q) use ($vehicleConditionIds) {
                $q->whereIn('vehicle_conditions_id', $vehicleConditionIds);
            });
        }

        if ($request->filled('displacement_from') || $request->filled('displacement_to')) {
            $query->where(function ($q) use ($request) {
                if ($request->filled('displacement_from')) {
                    $q->where('engine_displacement', '>=', $request->displacement_from);
                }

                if ($request->filled('displacement_to')) {
                    $q->where('engine_displacement', '<=', $request->displacement_to);
                }
            });
        }

        if ($request->filled('body_type_id')) {
            $bodyTypeIds = (array) $request->body_type_id; // force array
            $query->whereIn('body_type_id', $bodyTypeIds);
        }

        if ($request->filled('fuel_id')) {
            $fuelIds = (array) $request->fuel_id;
            $query->whereIn('fuel_id', $fuelIds);
        }

        if ($request->filled('transmission_id')) {
            $transmissionIds = (array) $request->transmission_id;
            $query->whereIn('transmission_id', $transmissionIds);
        }

        if ($request->filled('service_history')) {
            $query->whereHas('conditionAndMaintenance', function ($q) use ($request) {
                $q->where('service_history', $request->service_history);
            });
        }
        if ($request->filled('damaged_vehicle')) {
            $query->whereHas('conditionAndMaintenance', function ($q) use ($request) {
                $q->where('damaged_vehicle', $request->damaged_vehicle);
            });
        }

        $query->when(
            $request->filled('power_from') || $request->filled('power_to') || $request->filled('power_unit'),
            function ($q) use ($request) {
                $q->whereHas('power', function ($q2) use ($request) {
                    // Value filter
                    if ($request->filled('power_from') && $request->filled('power_to')) {
                        $q2->whereBetween('value', [$request->power_from, $request->power_to]);
                    } elseif ($request->filled('power_from')) {
                        $q2->where('value', '>=', $request->power_from);
                    } elseif ($request->filled('power_to')) {
                        $q2->where('value', '<=', $request->power_to);
                    }
                    // Unit filter
                    if ($request->filled('power_unit')) {
                        $q2->where('unit', $request->power_unit);
                    }
                });
            }
        );



        if ($request->filled('first_registration_from') && $request->filled('first_registration_to')) {
            $query->whereBetween('first_registration', [$request->first_registration_from, $request->first_registration_to]);
        } elseif ($request->filled('first_registration_from')) {
            $query->where('first_registration', '>=', $request->first_registration_from);
        } elseif ($request->filled('first_registration_to')) {
            $query->where('first_registration', '<=', $request->first_registration_to);
        }
        if ($request->filled('mileage_from') && $request->filled('mileage_to')) {
            $query->whereBetween('milage', [$request->mileage_from, $request->mileage_to]);
        } elseif ($request->filled('mileage_from')) {
            $query->where('milage', '>=', $request->mileage_from);
        } elseif ($request->filled('mileage_to')) {
            $query->where('milage', '<=', $request->mileage_to);
        }

        if ($request->filled('door_count_from') || $request->filled('door_count_to')) {
            $query->whereHas('data.numOfDoor', function ($q) use ($request) {
                if ($request->filled('door_count_from') && $request->filled('door_count_to')) {
                    $q->whereBetween('number', [$request->door_count_from, $request->door_count_to]);
                } elseif ($request->filled('door_count_from')) {
                    $q->where('number', '>=', $request->door_count_from);
                } elseif ($request->filled('door_count_to')) {
                    $q->where('number', '<=', $request->door_count_to);
                }
            });
        }
        if ($request->filled('bed_count_from') || $request->filled('bed_count_to')) {
            $query->whereHas('data.bedCount', function ($q) use ($request) {
                if ($request->filled('bed_count_from') && $request->filled('bed_count_to')) {
                    $q->whereBetween('number', [$request->bed_count_from, $request->bed_count_to]);
                } elseif ($request->filled('bed_count_from')) {
                    $q->where('number', '>=', $request->bed_count_from);
                } elseif ($request->filled('bed_count_to')) {
                    $q->where('number', '<=', $request->bed_count_to);
                }
            });
        }
        if ($request->filled('bed_type_id')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->whereIn('bed_type_id', (array) $request->bed_type_id);
            });
        }

        if ($request->filled('previous_owner_id')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->where('previous_owner_id', $request->previous_owner_id);
            });
        }
        if ($request->filled('number_of_seats_from') || $request->filled('number_of_seats_to')) {
            $query->whereHas('data.numOfSeats', function ($q) use ($request) {
                if ($request->filled('number_of_seats_from') && $request->filled('number_of_seats_to')) {
                    $q->whereBetween('number', [$request->number_of_seats_from, $request->number_of_seats_to]);
                } elseif ($request->filled('number_of_seats_from')) {
                    $q->where('number', '>=', $request->number_of_seats_from);
                } elseif ($request->filled('number_of_seats_to')) {
                    $q->where('number', '<=', $request->number_of_seats_to);
                }
            });
        }

        $equipmentIds = $request->input('equipment_ids', []);

        if (!empty($equipmentIds)) {
            $query->where(function ($q) use ($equipmentIds) {
                foreach ($equipmentIds as $id) {
                    $q->orWhereJsonContains('equipment_ids', $id); // integer cast optional
                }
            });
        }

        if ($request->filled('seller_type_id')) {
            $query->where('seller_type_id', $request->seller_type_id);
        }
        if ($request->filled('guarantee')) {
            $query->whereHas('conditionAndMaintenance', function ($q) use ($request) {
                $q->where('guarantee', $request->guarantee); // 1 or 0
            });
        }
        if ($request->filled('technical_inspection_valid_until')) {
            if ($request->technical_inspection_valid_until == 1) {
                $query->whereHas('conditionAndMaintenance', function ($q) {
                    $q->whereNotNull('technical_inspection_valid_until');
                });
            } else {
                $query->whereHas('conditionAndMaintenance', function ($q) {
                    $q->whereNull('technical_inspection_valid_until');
                });
            }
        }
        if ($request->filled('body_color_id')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->whereIn('body_color_id', (array) $request->body_color_id);
            });
        }

        if ($request->filled('interior_color_id')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->whereIn('interior_color_id', (array) $request->interior_color_id);
            });
        }

        if ($request->filled('upholstery_id')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->whereIn('upholstery_id', (array) $request->upholstery_id);
            });
        }

        if ($request->filled('metalic')) {
            $query->whereHas('data', function ($q) use ($request) {
                $q->where('metalic', (int) $request->metalic);
            });
        }

        if ($request->filled('emission_classes_id')) {
            $query->whereHas('engineAndEnvironment', function ($q) use ($request) {
                $q->where('emission_classes_id', $request->emission_classes_id);
            });
        }
        if ($request->filled('catalytic_converter')) {
            $query->whereHas('engineAndEnvironment', function ($q) use ($request) {
                $q->where('catalytic_converter', (int) $request->catalytic_converter);
            });
        }
        // Filter by Axle Count
        if ($request->filled('axle_count_id')) {
            $query->whereHas('engineAndEnvironment', function ($q) use ($request) {
                $q->where('axle_count_id', $request->axle_count_id);
            });
        }

        // Filter by Perm GVW
        if ($request->filled('perm_gvw')) {
            $query->whereHas('engineAndEnvironment', function ($q) use ($request) {
                $q->where('perm_gvw', 'like', "%{$request->perm_gvw}%");
            });
        }
        if ($request->filled('particle_filter')) {
            $query->whereHas('engineAndEnvironment', function ($q) use ($request) {
                $q->where('particle_filter', (int) $request->particle_filter);
            });
        }
        if ($request->filled('posting_age') && is_numeric($request->posting_age)) {
            $query->whereDate('vehicles.created_at', '>=', now()->subDays($request->posting_age));
        }
        if ($request->filled('add_with_pictures') && $request->add_with_pictures == 1) {
            $query->whereHas('photos');
        }

        // to view in response
        $bodyColorNames = [];
        if ($request->filled('body_color_id')) {
            $bodyColorNames = BodyColor::whereIn('id', (array)$request->body_color_id)
                ->pluck('name')
                ->toArray();
        }

        $interiorColorNames = [];
        if ($request->filled('interior_color_id')) {
            $interiorColorNames = InteriorColor::whereIn('id', (array)$request->interior_color_id)
                ->pluck('name')
                ->toArray();
        }

        $upholsteryNames = [];
        if ($request->filled('upholstery_id')) {
            $upholsteryNames = Upholstery::whereIn('id', (array)$request->upholstery_id)
                ->pluck('name')
                ->toArray();
        }
        $equipmentNames = [];
        if ($request->filled('equipment_ids')) {
            $equipmentNames = Equipment::whereIn('id', $request->equipment_ids)
                ->pluck('title')
                ->toArray();
        }
        $bedTypeNames = [];
        if ($request->filled('bed_type_id')) {
            $bedTypeNames = \App\Models\BedType::whereIn('id', (array) $request->bed_type_id)
                ->pluck('name')
                ->toArray();
        }
        $bodyTypeNames = [];
        if ($request->filled('body_type_id')) {
            $bodyTypeNames = \App\Models\BodyType::whereIn('id', (array) $request->body_type_id)
                ->pluck('title')
                ->toArray();
        }
        // Get default radius from system settings
        $defaultRadius = optional(SystemSetting::first())->radius;
        // Location filter (lat/lng)
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->input('radius', $defaultRadius); // user input or default
            $query->join('contact_infos', 'contact_infos.vehicle_id', '=', 'vehicles.id')
                ->select('vehicles.*')
                ->selectRaw("(6371 * acos(
                cos(radians(?)) *
                cos(radians(contact_infos.lat)) *
                cos(radians(contact_infos.lng) - radians(?)) +
                sin(radians(?)) *
                sin(radians(contact_infos.lat))
            )) AS distance", [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        }

        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('vehicles.price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('vehicles.price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('vehicles.created_at', 'desc');
                    break;
                case 'registration_newest':
                    $query->orderBy('vehicles.first_registration', 'desc');
                    break;
                case 'registration_oldest':
                    $query->orderBy('vehicles.first_registration', 'asc');
                    break;
                case 'mileage_asc':
                    $query->orderBy('vehicles.milage', 'asc');
                    break;
                case 'mileage_desc':
                    $query->orderBy('vehicles.milage', 'desc');
                    break;
                case 'power_desc':
                    $query->join('powers', 'vehicles.power_id', '=', 'powers.id')
                          ->select('vehicles.*')
                          ->orderBy('powers.value', 'desc');
                    break;
                case 'power_asc':
                    $query->join('powers', 'vehicles.power_id', '=', 'powers.id')
                          ->select('vehicles.*')
                          ->orderBy('powers.value', 'asc');
                    break;

                default:
                    $query->latest('vehicles.created_at');
                    break;
            }
        } else {
            $query->latest('vehicles.created_at');
        }

        $vehicles = $query->latest()->paginate(20);

        if ($user = auth('sanctum')->user()) {

            // ১. একটি ইন্টারনাল ফাংশন যা অ্যারের ভেতর পর্যন্ত ক্লিন করবে
            $cleanArray = function ($array) use (&$cleanArray) {
                foreach ($array as $key => &$value) {
                    if (is_array($value)) {
                        $value = $cleanArray($value); // অ্যারের ভেতর আবার চেক করবে
                    }
                    // যদি ভ্যালু null, খালি অ্যারে [], অথবা খালি স্ট্রিং হয় তবে তা রিমুভ করো
                    if (is_null($value) || (is_array($value) && empty($value)) || $value === '') {
                        unset($array[$key]);
                    }
                }
                return $array;
            };

            $logFilters = [
                'budget' => ['from' => $request->budget_from, 'to' => $request->budget_to],
                'mileage' => ['from' => $request->mileage_from, 'to' => $request->mileage_to],
                'registration' => ['from' => $request->first_registration_from, 'to' => $request->first_registration_to],
                'power' => [
                    'from' => $request->power_from,
                    'to' => $request->power_to,
                    'unit' => $request->power_unit
                ],
                'category' => $request->category_id ? [
                    'id' => $request->category_id,
                    'name' => \App\Models\Category::find($request->category_id)?->name
                ] : null,
                'brand' => $request->brand_id ? [
                    'id' => $request->brand_id,
                    'name' => \App\Models\Brand::find($request->brand_id)?->name
                ] : null,
                'models' => $request->filled('model_id') ? \App\Models\CarModel::whereIn('id', (array)$request->model_id)->get(['id', 'name'])->toArray() : null,
                'sub_models' => $request->filled('sub_model_id') ? \App\Models\SubModel::whereIn('id', (array)$request->sub_model_id)->get(['id', 'name'])->toArray() : null,
                'fuel' => $request->fuel_id ? [
                    'id' => $request->fuel_id,
                    'name' => \App\Models\Fuel::find($request->fuel_id)?->title
                ] : null,
                'transmission' => $request->transmission_id ? [
                    'id' => $request->transmission_id,
                    'name' => \App\Models\Transmission::find($request->transmission_id)?->title
                ] : null,
                'body_types' => $request->filled('body_type_id') ? \App\Models\BodyType::whereIn('id', (array)$request->body_type_id)->get(['id', 'title as name'])->toArray() : null,
                'equipments' => $request->filled('equipment_ids') ? \App\Models\Equipment::whereIn('id', (array)$request->equipment_ids)->get(['id', 'title as name'])->toArray() : null,
                'colors' => [
                    'body' => $bodyColorNames ?? [],
                    'interior' => $interiorColorNames ?? []
                ],
                'location' => $request->filled('lat') ? [
                    'lat' => $request->lat, 'lng' => $request->lng, 'radius' => $radius ?? null
                ] : null,
                'others' => [
                    'damaged_vehicle' => $request->damaged_vehicle,
                    'service_history' => $request->service_history,
                    'seller_type' => $request->seller_type_id,
                ]
            ];

            // ২. ডেটাবেসে ক্লিন করা ফিল্টারটি সেভ করুন
            UserSearchLog::create([
                'user_id'       => $user->id,
                'filters'       => $cleanArray($logFilters), // এখানে ক্লিন ফাংশনটি কল করা হয়েছে
                'results_count' => $vehicles->total(),
                'ip_address'    => $request->ip(),
            ]);
        }

        foreach ($vehicles as $vehicle) {
            SearchLog::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => auth()->id() ?? null,
                'searched_at' => now(),
            ]);
        }
        if ($vehicles->isEmpty()) {
            return $this->success([
                'filters'  => [],
                'vehicles' => [],
            ], 'No results found', 200);
        }
        $filters = [
            'category' => $request->category_id ? optional($vehicles->first()?->category)->name : null,
            'brand'    => $request->brand_id ? optional($vehicles->first()?->brand)->name : null,
            'model'    => $request->model_id ? optional($vehicles->first()?->model)->name : null,
            'sub_model' => $request->sub_model_id ? optional($vehicles->first()?->subModel)->name : null,
            'lat'      => $request->lat ?? null,
            'lng'      => $request->lng ?? null,
            'radius'   => $radius ?? null,
            'budget_from'  => $request->budget_from ?? null,
            'budget_to'    => $request->budget_to ?? null,
            'indicate_vat' => $request->filled('indicate_vat') ? (int) $request->indicate_vat : null,
            'vehicle_conditions' => $request->vehicle_conditions_id ? optional($vehicles->first()?->data->condition)->name : null,
            'first_registration_from' => $request->first_registration_from ?? null,
            'first_registration_to'   => $request->first_registration_to ?? null,
            'mileage_from' => $request->mileage_from ?? null,
            'mileage_to'   => $request->mileage_to ?? null,
            'door_count_from' => $request->door_count_from ?? null,
            'door_count_to'   => $request->door_count_to ?? null,
            'number_of_seats_from' => $request->number_of_seats_from ?? null,
            'number_of_seats_to'   => $request->number_of_seats_to ?? null,
            'body_type' => $bodyTypeNames ?? null,
            'fuel' => $request->fuel_id ? optional($vehicles->first()?->fuel)->title : null,
            'transmission' => $request->transmission_id ? optional($vehicles->first()?->transmission)->title : null,
            'previous_owner' => $request->previous_owner_id
                ? optional($vehicles->first()?->data?->previousOwner)->number
                : null,
            'power_range' => ($request->filled('power_from') || $request->filled('power_to') || $request->filled('power_unit'))
                ? [
                    'from' => $request->power_from ?? null,
                    'to'   => $request->power_to ?? null,
                    'unit' => $request->power_unit ?? null, // add unit
                ]
                : null,

            'service_history' => $request->filled('service_history')
                ? (int) $request->service_history
                : null,
            'damaged_vehicle' => $request->filled('damaged_vehicle')
                ? (int) $request->damaged_vehicle
                : null,
            'equipment' => $request->filled('equipment_ids') ? $equipmentNames : null,
            'seller_type' => $request->filled('seller_type_id')
                ? optional($vehicles->first()?->seller_type)->title
                : null,
            'guarantee' => $request->filled('guarantee') ? (int) $request->guarantee : null,
            'technical_inspection_valid_until' => $request->filled('technical_inspection_valid_until')
                ? (int) $request->technical_inspection_valid_until
                : null,
            'body_color' => $bodyColorNames,
            'interior_color' => $interiorColorNames,
            'upholstery' => $upholsteryNames,
            'metalic' => $request->filled('metalic')
                ? (int) $request->metalic
                : null,
            'engine_emission_class' => $request->filled('emission_classes_id')
                ? optional($vehicles->first()?->engineAndEnvironment?->emissionClass)->title
                : null,
            'catalytic_converter' => $request->filled('catalytic_converter')
                ? $request->catalytic_converter
                : null,

            'particle_filter' => $request->filled('particle_filter')
                ? $request->particle_filter
                : null,

            'bed_type' => $bedTypeNames ?? null,

            // ✅ Request value precedence
            'axle_count' => $request->filled('axle_count_id')
                ? $request->axle_count_id
                : null,

            'perm_gvw' => $request->filled('perm_gvw')
                ? $request->perm_gvw
                : null,
        ];
        // if ($user = auth('sanctum')->user()) {
        //     UserSearchLog::create([
        //         'user_id'       => $user->id,
        //         'filters'       => $request->except(['page']),
        //         'results_count' => $vehicles->total(),
        //         'ip_address'    => $request->ip(),
        //     ]);
        // }


        return $this->success([
            'filters'  => $filters,
            'vehicles' => $vehicles,
        ], 'Vehicle Fetched Successfully', 200);
    }
    // public function latestPopular(Request $request)
    // {
    //     $vehicles = Vehicle::select(
    //         'vehicles.id',
    //         'vehicles.category_id',
    //         'vehicles.brand_id',
    //         'vehicles.model_id',
    //         'vehicles.body_type_id',
    //         'vehicles.sub_model_id',
    //         'vehicles.power_id',
    //         'vehicles.price',
    //         'vehicles.first_registration'
    //     )
    //         ->with(['category', 'brand', 'model', 'subModel', 'body_type', 'photos', 'contactInfo', 'power', 'data.condition']) // eager load relations
    //         ->leftJoin('search_logs', 'vehicles.id', '=', 'search_logs.vehicle_id')
    //         ->groupBy('vehicles.id')
    //         ->orderByDesc(DB::raw('COUNT(search_logs.id)')) // search count
    //         ->orderByDesc(DB::raw('MAX(search_logs.searched_at)')) // latest search
    //         ->paginate(20);

    //     $totalVehicles = $vehicles->total();

    //     return $this->success([
    //         'total' => $totalVehicles,
    //         'data' => $vehicles->items()
    //     ], 'Vehicles fetched successfully', 200);
    // }
    public function latestPopular(Request $request)
    {
        // Step 1: Aggregate search logs (popularity data)
        $popular = DB::table('search_logs')
            ->select(
                'vehicle_id',
                DB::raw('COUNT(*) as search_count'),
                DB::raw('MAX(searched_at) as last_search')
            )
            ->groupBy('vehicle_id');

        // Step 2: Join with vehicles table + eager load relationships
        $vehicles = Vehicle::select('vehicles.*')
            ->leftJoinSub($popular, 'pop', function ($join) {
                $join->on('vehicles.id', '=', 'pop.vehicle_id');
            })
            ->with([
                'category',
                'brand',
                'model',
                'subModel',
                'body_type',
                'photos',
                'contactInfo',
                'contactInfo.country',
                'contactInfo.city',
                'power',
                'data.condition'
            ])
            ->orderByDesc(DB::raw('COALESCE(pop.search_count, 0)'))
            ->orderByDesc('pop.last_search')
            ->paginate(20);

        // Step 3: Get total count (for frontend pagination)
        $totalVehicles = $vehicles->total();

        // Step 4: Return success response
        return $this->success([
            'total' => $totalVehicles,
            'data' => $vehicles->items()
        ], 'Vehicles fetched successfully', 200);
    }
    public function getFilteredSearch()
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->error('Unauthorized', 401);
        }

        // ইউজারের সর্বশেষ সার্চ লগটি নিয়ে আসা
        $lastSearch = UserSearchLog::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$lastSearch) {
            return $this->success(null, 'No search history found', 200);
        }

        // যদি filters কলামটি JSON হিসেবে সেভ থাকে এবং মডেলে Cast করা থাকে,
        // তবে এটি সরাসরি অ্যারে হিসেবে আসবে।
        return $this->success([
            'log_id'           => $lastSearch->id,
            'last_searched_at' => $lastSearch->created_at->diffForHumans(),
            'results_found'    => $lastSearch->results_count,
            'filters'          => $lastSearch->filters, // এখানে এখন ID এবং Name দুইটাই আছে
        ], 'Last search filters fetched successfully');
    }
}
