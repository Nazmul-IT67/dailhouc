<?php

namespace App\Http\Controllers\API\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\AxleCount;
use App\Models\BedCount;
use App\Models\BedType;
use App\Models\BodyColor;
use App\Models\BodyType;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Cylinder;
use App\Models\DriverType;
use App\Models\EmissionClass;
use App\Models\Equipment;
use App\Models\EquipmentLine;
use App\Models\Fuel;
use App\Models\InteriorColor;
use App\Models\NumberOfDoor;
use App\Models\NumberOfSeat;
use App\Models\NumOfGear;
use App\Models\Power;
use App\Models\PreviousOwner;
use App\Models\SellerType;
use App\Models\Transmission;
use App\Models\Upholstery;
use App\Models\Vehicle;
use App\Models\ModelYear;
use App\Models\VehicleCondition;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    use ApiResponse;

    // getCategory
    public function getCategory(Request $request)
    {
        $language = $request->query('language', app()->getLocale());

        if ($language == 'fr') {
            $categories = Category::whereHas('translations', function ($q) use ($language) {
                $q->where('language', $language);
            })->with(['translations' => function ($q) use ($language) {
                $q->where('language', $language);
            }])->get();
        } else {
            $categories = Category::all();
        }

        $data = $categories->map(function ($category) use ($language) {
            if ($language == 'fr') {
                $translation = $category->translations->first();
                $name = $translation ? $translation->name : $category->name;
            } else {
                $name = $category->name;
            }

            return [
                'id'   => $category->id,
                'name' => $name,
                'slug' => $category->slug,
            ];
        });

        if ($data->isEmpty()) {
            return $this->success([], 'No categories found for this language.', 200);
        }

        return $this->success($data, 'Categories Fetched Successfully!', 200);
    }

    // getBrandByCategory
    public function getBrandByCategory(Request $request)
    {
        $category_id = $request->category_id;
        $language = $request->query('language');

        if (!$category_id) {
            return $this->error([], 'Category ID is required', 400);
        }

        $brands = Brand::where('category_id', $category_id)
            ->withCount('vehicles')
            ->when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->get();

        if ($brands->isEmpty()) {
            return $this->success([], 'No brands found for this category', 200);
        }

        if ($language) {
            $brands->transform(function ($brand) {
                if ($brand->translations->isNotEmpty()) {
                    $translation = $brand->translations->first();

                    $brand->name = $translation->name ?? $brand->name;
                }

                return $brand->makeHidden('translations');
            });
        }

        return $this->success($brands, 'Brands fetched successfully', 200);
    }

    // getModelsByBrand
    public function getModelsByBrand(Request $request)
    {
        $brand_id = $request->brand_id;
        $language = $request->query('language');

        if (!$brand_id) {
            return $this->error([], 'Brand ID is required', 400);
        }

        $models = CarModel::where('brand_id', $brand_id)
            ->withCount('vehicles')
            ->when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->get();

        if ($models->isEmpty()) {
            return $this->success([], 'No Models found for this Brand', 200);
        }

        if ($language) {
            $models->transform(function ($model) {
                if ($model->translations->isNotEmpty()) {
                    $translation = $model->translations->first();
                    $model->name = $translation->name ?? $model->name;
                }
                return $model->makeHidden('translations');
            });
        }

        return $this->success($models, 'Models fetched successfully', 200);
    }

    // getSubModelsByModel
    public function getSubModelsByModel(Request $request)
    {
        $car_model_id = $request->car_model_id;
        $language = $request->query('language', 'en');

        if (!$car_model_id) {
            return $this->error([], 'Car Model ID is required', 400);
        }

        $carModel = CarModel::with(['subModels.translations' => function ($q) use ($language) {
            $q->where('language', $language);
        }, 'translations' => function ($q) use ($language) {
            $q->where('language', $language);
        }])
            ->withCount('vehicles')
            ->find($car_model_id);

        if (!$carModel) {
            return $this->error([], 'Car Model not found', 404);
        }

        $subModels = $carModel->subModels->map(function ($subModel) {
            return [
                'id'           => $subModel->id,
                'car_model_id' => $subModel->car_model_id,
                'name'         => $subModel->translations->first()->name ?? $subModel->name,
            ];
        });

        $modelName = $carModel->translations->first()->name ?? $carModel->name;

        return $this->success([
            'model_name'     => $modelName,
            'total_vehicles' => $carModel->vehicles_count,
            'sub_models'     => $subModels
        ], 'SubModels fetched successfully', 200);
    }

    // getModelsWithSubModelsByBrand
    public function getModelsWithSubModelsByBrand(Request $request)
    {
        $brand_id = $request->brand_id;
        $language = $request->query('language');

        if (!$brand_id) {
            return $this->error([], 'Brand ID is required', 400);
        }

        // Fetch models with nested submodels and translations
        $models = CarModel::where('brand_id', $brand_id)
            ->withCount('vehicles')
            ->with([
                'subModels' => function ($q) {
                    $q->withCount('vehicles');
                }
            ])
            // Language thakle Model ebong SubModel-er translation load hobe
            ->when($language, function ($q) use ($language) {
                $q->with([
                    'translations' => fn ($t) => $t->where('language', $language),
                    'subModels.translations' => fn ($st) => $st->where('language', $language)
                ]);
            })
            ->get();

        if ($models->isEmpty()) {
            return $this->success([], 'No Models or SubModels for this Brand', 200);
        }

        // Data transform kora apnar style onusare
        if ($language) {
            $models->transform(function ($model) {
                // Main Model Name Translation
                if ($model->translations->isNotEmpty()) {
                    $translation = $model->translations->first();
                    $model->name = $translation->name ?? $model->name;
                }

                // SubModels Name Translation
                $model->subModels->transform(function ($sub) {
                    if ($sub->translations->isNotEmpty()) {
                        $subTranslation = $sub->translations->first();
                        $sub->name = $subTranslation->name ?? $sub->name;
                    }
                    return $sub->makeHidden('translations');
                });

                return $model->makeHidden('translations');
            });
        }

        return $this->success($models, 'Models and SubModels fetched successfully', 200);
    }

    // getVehicleConditions
    public function getVehicleConditions(Request $request)
    {
        $language = $request->query('language');

        $data = VehicleCondition::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })
            ->orderBy('name')
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Vehicle Conditions found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->name = $translation->name ?? $item->name;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Vehicle Conditions fetched successfully', 200);
    }

    // getBodyColors
    public function getBodyColors(Request $request)
    {
        $language = $request->query('language');

        $data = BodyColor::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('name')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Body Color found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->name = $translation->name ?? $item->name;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Body Colors fetched successfully', 200);
    }

    // getUpholsteries
    public function getUpholsteries(Request $request)
    {
        $language = $request->query('language');
        $categoryId = $request->query('category_id');

        $data = Upholstery::when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->withCount(['vehicleData as categories_count' => function ($query) use ($categoryId) {
                $query->join('vehicles', 'vehicle_data.vehicle_id', '=', 'vehicles.id');
                
                if ($categoryId) {
                    $query->where('vehicles.category_id', $categoryId);
                }
                
                $query->select(\DB::raw('count(distinct(vehicles.category_id))'));
            }])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Upholsteries found', 200);
        }

        $data->transform(function ($item) use ($language) {
            if ($language && $item->translations->isNotEmpty()) {
                $translation = $item->translations->first();
                $item->name = $translation->name ?? $item->name;
            }
            return $item->makeHidden('translations');
        });

        return $this->success($data, 'Upholsteries fetched successfully', 200);
    }

    // getInteriorColors
    public function getInteriorColors(Request $request)
    {
        $language = $request->query('language');
        $categoryId = $request->query('category_id');

        $data = InteriorColor::when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->withCount(['vehicleData as vehicle_count' => function ($query) use ($categoryId) {
                $query->join('vehicles', 'vehicle_data.vehicle_id', '=', 'vehicles.id');
                if ($categoryId) {
                    $query->where('vehicles.category_id', $categoryId);
                }
                $query->select(\DB::raw('count(distinct(vehicles.category_id))'));
            }])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Interior color found', 200);
        }

        $data->transform(function ($item) use ($language) {
            if ($language && $item->translations->isNotEmpty()) {
                $item->name = $item->translations->first()->name ?? $item->name;
            }
            return $item->makeHidden('translations');
        });

        return $this->success($data, 'Interior Colors fetched successfully', 200);
    }

    // getPreviousOwners
    public function getPreviousOwners()
    {
        $data = PreviousOwner::orderBy('number')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No of previous Owner not found', 200);
        }
        return $this->success($data, 'Previous Owners fetched successfully', 200);
    }

    // getNumberOfDoors
    public function getNumberOfDoors()
    {
        $data = NumberOfDoor::orderBy('number')->get();
        if ($data->isEmpty()) {
            return $this->error([], 'No. of door not found', 200);
        }
        return $this->success($data, 'Number of Doors fetched successfully', 200);
    }

    // getNumberOfSeats
    public function getNumberOfSeats()
    {
        $data = NumberOfSeat::orderBy('number')->get();
        if ($data->isEmpty()) {
            return $this->error([], 'No seats found', 200);
        }
        return $this->success($data, 'Number of Seats fetched successfully', 200);
    }

    // getBedCounts
    public function getBedCounts(Request $request)
    {
        $language = $request->query('language');

        $data = BedCount::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('number')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Bed Count found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->number = $translation->number ?? $item->number;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Bed Counts fetched successfully', 200);
    }

    // getBedTypes
    public function getBedTypes(Request $request)
    {
        $language = $request->query('language');

        $data = BedType::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('name')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No bed type found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->name = $translation->name ?? $item->name;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Bed Types fetched successfully', 200);
    }

    // getDriverTypes
    public function getDriverTypes(Request $request)
    {
        $language = $request->query('language');

        $data = DriverType::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('title')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Driver Type Found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->title = $translation->title ?? $item->title;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Driver Types fetched successfully', 200);
    }

    // getTransmission
    public function getTransmission(Request $request)
    {
        $language = $request->query('language', app()->getLocale());
        $categoryId = $request->query('category_id');

        $data = Transmission::when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->withCount(['vehicles as vehicles_count' => function ($query) use ($categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                $query->select(\DB::raw('count(distinct(category_id))'));
            }])->orderBy('id')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Transmission Found', 200);
        }

        $data->transform(function ($item) use ($language) {
            if ($language && $item->translations->isNotEmpty()) {
                $translation = $item->translations->first();
                $item->title = $translation->title ?? $item->title;
            }
            return $item->makeHidden('translations');
        });

        return $this->success($data, 'Transmission fetched successfully', 200);
    }

    // getNumOfGears
    public function getNumOfGears()
    {
        $data = NumOfGear::get();
        if ($data->isEmpty()) {
            return $this->error([], 'No number of gears Found', 200);
        }
        return $this->success($data, 'Number of gears fetched successfully', 200);
    }

    // getNumOfCylinders
    public function getNumOfCylinders()
    {
        $data = Cylinder::get();
        if ($data->isEmpty()) {
            return $this->error([], 'No number of Cylinder Found', 200);
        }
        return $this->success($data, 'Cylinder fetched successfully', 200);
    }

    // getEmissionClasses
    public function getEmissionClasses(Request $request)
    {
        $language = $request->query('language');

        $data = EmissionClass::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('title')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Emission class Found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->title = $translation->title ?? $item->title;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Emission class fetched successfully', 200);
    }

    // getFuelTypes
    public function getFuelTypes(Request $request)
    {
        $language = $request->query('language');
        $categoryId = $request->query('category_id');

        $fuels = Fuel::with(['translations' => function ($q) use ($language) {
                if ($language) {
                    $q->where('language', $language);
                }
            }])
            ->withCount(['vehicles as vehicles_count' => function ($query) use ($categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                $query->select(\DB::raw('count(distinct(category_id))'));
            }])
            ->get();

        if ($fuels->isEmpty()) {
            return $this->error([], 'No Fuel Types Found', 200);
        }

        foreach ($fuels as $fuel) {
            if ($language && $fuel->translations->isNotEmpty()) {
                $fuel->title = $fuel->translations->first()->title ?? $fuel->title;
            }
            $fuel->makeHidden('translations');
        }

        return $this->success($fuels, 'Fuel Types fetched successfully', 200);
    }

    // getBodyTypes
    public function getBodyTypes(Request $request)
    {
        $language = $request->query('language');

        $query = BodyType::where('category_id', $request->category_id)
            ->withCount('vehicles')
            ->when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            });

        $data = $query->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Body Types Found for this Category', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();

                    if (isset($item->title)) {
                        $item->title = $translation->title ?? $translation->name;
                    }
                    $item->name = $translation->name ?? $translation->title;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Body Types fetched successfully', 200);
    }

    // getPower
    public function getPower(Request $request)
    {
        $data = Power::orderBy('value')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Power Found', 200);
        }

        return $this->success($data, 'Power fetched successfully', 200);
    }

    // getAxleCount
    public function getAxleCount()
    {
        $data = AxleCount::orderBy('count')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Axle Count Found', 200);
        }

        return $this->success($data, 'Axle Count fetched successfully', 200);
    }

    // getEquipmentLine
    public function getEquipmentLine(Request $request)
    {
        $language = $request->query('language');

        $data = EquipmentLine::when($language, function ($q) use ($language) {
            $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
        })->orderBy('title')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Equipment Line Found', 200);
        }

        if ($language) {
            $data->transform(function ($item) {
                if ($item->translations->isNotEmpty()) {
                    $translation = $item->translations->first();
                    $item->title = $translation->title ?? $item->title;
                }
                return $item->makeHidden('translations');
            });
        }

        return $this->success($data, 'Equipment Line fetched successfully', 200);
    }

    // getSellerTypes
    public function getSellerTypes(Request $request)
    {
        $language = $request->query('language');
        $categoryId = $request->query('category_id');

        $data = SellerType::when($language, function ($q) use ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            })
            ->withCount(['vehicles as vehicles_count' => function ($query) use ($categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                $query->select(\DB::raw('count(distinct(category_id))'));
            }])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No Seller Types Found', 200);
        }

        $data->transform(function ($item) use ($language) {
            if ($language && $item->translations->isNotEmpty()) {
                $translation = $item->translations->first();
                $item->title = $translation->title ?? $item->title;
            }
            return $item->makeHidden('translations');
        });

        return $this->success($data, 'Seller Types fetched successfully', 200);
    }

    // getEquipment
    public function getEquipment(Request $request)
    {
        $query = Equipment::query();
        $language = $request->query('language');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $equipment = $query->get();

        if ($equipment->isEmpty()) {
            return $this->error([], 'No Equipment Found', 200);
        }

        foreach ($equipment as $data) {
            if ($language) {
                $translation = $data->translations->first();
                if ($translation) {
                    $data->title = $translation->title;
                }
            }
            $data->makeHidden('translations');
        }

        return $this->success($equipment, 'Equipment fetched successfully', 200);
    }

    // toggleFavorite
    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated. Please log in to access your vehicles.', 200);
        }

        $vehicleId = $request->vehicle_id;

        // Check if vehicle exists
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            return $this->error([], 'Vehicle not found', 200);
        }

        $favorite = $user->favoriteVehicles()->where('vehicle_id', $vehicleId)->first();

        if ($favorite) {
            $user->favoriteVehicles()->detach($vehicleId);
            return $this->success([], 'Vehicle removed from favorites', 200);
        } else {
            $user->favoriteVehicles()->attach($vehicleId, ['created_at' => now(), 'updated_at' => now()]);

            // Fetch pivot info after attaching
            $pivot = $user->favoriteVehicles()->where('vehicle_id', $vehicleId)->first()->pivot;

            return $this->success([
                'vehicle' => $vehicle,
                'favorite' => [
                    'created_at' => $pivot->created_at,
                    'updated_at' => $pivot->updated_at
                ]
            ], 'Vehicle added to favorites', 200);
        }
    }

    // getFavoriteVehicles
    public function getFavoriteVehicles(Request $request)
    {
        $user = Auth::user();
        $lang = $request->query('language');

        if (!$user) {
            return $this->error([], 'Unauthenticated.', 200);
        }

        $withTr = function($q) use ($lang) {
            if ($lang) {
                $q->with(['translations' => fn($t) => $t->where('language', $lang)]);
            }
        };

        $vehicles = Vehicle::whereHas('favoritedBy', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with([
                'category' => $withTr, 'brand' => $withTr, 'model' => $withTr, 
                'subModel' => $withTr, 'photos', 'power', 'currency', 'baseCurrency',
                'contactInfo.country', 'contactInfo.city', 'data.condition', 'fuel',
                'user' => function($q) use ($withTr) {
                    $q->with(['country' => $withTr, 'city' => $withTr]);
                }
            ])
            ->withExists(['favoritedBy as is_favorite' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $vehicles->getCollection()->transform(function ($v) use ($lang) {
            $v->is_favorite = (bool) $v->is_favorite;

            if ($v->contactInfo) {
                $v->contactInfo->country_name = $v->contactInfo->country?->name;
                $v->contactInfo->city_name = $v->contactInfo->city?->name;
                $v->contactInfo->makeHidden(['country', 'city']);
            }

            if ($lang) {
                $rels = ['category', 'brand', 'model', 'subModel', 'fuel', 'body_type', 'transmission'];
                foreach ($rels as $rel) {
                    if ($v->$rel && $v->$rel->translations->isNotEmpty()) {
                        $v->$rel->name = $v->$rel->translations->first()->name ?? $v->$rel->translations->first()->title;
                        $v->$rel->makeHidden('translations');
                    }
                }
            }
            return $v;
        });

        return $this->success($vehicles, 'Favorite vehicles fetched successfully', 200);
    }

    // getModelYears
    public function getModelYears()
    {
        $years = ModelYear::select('id', 'year')->orderBy('year', 'desc')->get();
        return $this->success($years, 'Model years fetched successfully', 200);
    }
}
