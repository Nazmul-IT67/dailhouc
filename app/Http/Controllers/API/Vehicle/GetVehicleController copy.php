<?php

namespace App\Http\Controllers\API\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth; // Correct import
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class GetVehicleController extends Controller
{
    use ApiResponse;

    // getAllVehicles
    public function getAllVehicles(Request $request)
    {
        $language = $request->query('language');

        $withTranslation = function ($q) use ($language) {
            if ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            }
        };

        $vehicles = Vehicle::select(
            'vehicles.id',
            'vehicles.category_id',
            'vehicles.brand_id',
            'vehicles.model_id',
            'vehicles.body_type_id',
            'vehicles.sub_model_id',
            'vehicles.power_id',
            'vehicles.price',
            'vehicles.price_in_base',
            'vehicles.currency_id',
            'vehicles.base_currency_id',
            'vehicles.first_registration'
        )
        ->with([
            'power', 'category' => $withTranslation, 'brand' => $withTranslation,  'model' => $withTranslation, 'subModel' => $withTranslation, 'body_type' => $withTranslation, 'data.condition' => $withTranslation, 'photos', 'contactInfo', 'currency', 'baseCurrency'
        ])->where('status', 1)->orderBy('created_at', 'desc')->paginate(20);

        $baseCurrency = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        if (!$baseCurrency) {
            return $this->error([], 'Base currency is not set!', 500);
        }

        $user = auth()->user();
        $viewerCurrency = $user?->country?->currency ?? $baseCurrency;

        $vehicles->getCollection()->transform(function ($vehicle) use ($baseCurrency, $viewerCurrency, $language) {

            if ($language) {
                $relations = ['category', 'brand', 'model', 'subModel', 'body_type'];
                foreach ($relations as $rel) {
                    if ($vehicle->$rel && $vehicle->$rel->translations->isNotEmpty()) {
                        $translated = $vehicle->$rel->translations->first();

                        if (isset($vehicle->$rel->title)) {
                            $vehicle->$rel->title = $translated->title ?? $translated->name;
                        }
                        $vehicle->$rel->name = $translated->name ?? $translated->title;

                        $vehicle->$rel->makeHidden('translations');
                    }
                }

                if ($vehicle->data && $vehicle->data->condition) {
                    if ($vehicle->data->condition->translations->isNotEmpty()) {
                        $vehicle->data->condition->name = $vehicle->data->condition->translations->first()->name;
                        $vehicle->data->condition->makeHidden('translations');
                    }
                    $vehicle->data->bed_types = $vehicle->data->bedTypes;
                }
            }

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

            return $vehicle;
        });

        return $this->success($vehicles, 'All vehicles fetched successfully!', 200);
    }

    public function getUsersVehicle(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated.', 200);
        }

        $language = $request->query('language');

        $withTranslation = function ($q) use ($language) {
            if ($language) {
                $q->with(['translations' => fn ($t) => $t->where('language', $language)]);
            }
        };

        $vehicles = Vehicle::select(
            'vehicles.id',
            'vehicles.category_id',
            'vehicles.brand_id',
            'vehicles.model_id',
            'vehicles.body_type_id',
            'vehicles.sub_model_id',
            'vehicles.power_id',
            'vehicles.price',
            'vehicles.price_in_base',
            'vehicles.currency_id',
            'vehicles.base_currency_id',
            'vehicles.first_registration',
            'vehicles.user_id'
        )
        ->with([
            'category' => $withTranslation,
            'brand' => $withTranslation,
            'model' => $withTranslation,
            'body_type' => $withTranslation,
            'subModel' => $withTranslation,
            'data.condition' => $withTranslation,
            'photos', 'currency', 'baseCurrency'
        ])
        ->where('status', 1)
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $baseCurrency = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewerCurrency = $request->user()?->country?->currency ?? $baseCurrency;

        $vehicles->getCollection()->transform(function ($v) use ($language, $baseCurrency, $viewerCurrency) {

            // --- ১. Main Relations Translation (Helper use kora holo) ---
            if ($language) {
                $this->mapTranslations($v, ['category', 'brand', 'model', 'body_type', 'subModel']);

                if ($v->data) {
                    $this->mapTranslations($v->data, ['condition']);
                }
            }

            // --- ২. Equipments Translation (Apnar perfectly working logic) ---
            $eqs = $v->equipments;
            if ($eqs && $eqs->isNotEmpty()) {
                foreach ($eqs as $equipment) {
                    if ($language) {
                        $trans = $equipment->translations()->where('language', $language)->first();
                        if ($trans) {
                            $equipment->title = $trans->title ?? $trans->name ?? $equipment->title;
                        }
                    }
                    $equipment->makeHidden(['translations', 'name']);
                }
                $v->setRelation('equipments', $eqs);
            }

            // --- ৩. Bed Types Translation ---
            if ($v->data && !empty($v->data->bed_type_id)) {
                $ids = (array) $v->data->bed_type_id;

                $bedTypesData = \App\Models\BedType::whereIn('id', $ids)
                    ->with(['translations' => function ($q) use ($language) {
                        if ($language) {
                            $q->where('language', $language);
                        }
                    }])
                    ->get();

                $translatedNames = $bedTypesData->map(function ($bed) use ($language) {
                    if ($language && $bed->translations->isNotEmpty()) {
                        // Ekhane check korun translation table-e 'name' field-i ache kina
                        return $bed->translations->first()->name ?? $bed->translations->first()->title ?? $bed->name;
                    }
                    return $bed->name;
                })->toArray();

                // Bed types overwrite kora
                $v->data->bed_types = $translatedNames;
                $v->data->makeHidden(['bed_type_id']);
            }

            // --- ৪. Pricing Logic ---
            $v->poster_price = number_format($v->price, 2);
            $v->poster_currency_symbol = $v->currency?->symbol ?? '';
            $v->base_price = number_format($v->price_in_base, 2);
            $v->base_currency_symbol = $v->baseCurrency?->symbol ?? '$';
            $v->viewer_price = number_format(convertPrice($v->price_in_base, $baseCurrency->code, $viewerCurrency->code), 2);
            $v->viewer_currency_symbol = $viewerCurrency->symbol;

            return $v;
        });

        return $this->success($vehicles, 'User vehicles fetched successfully!', 200);
    }
    // vehicleDetails
    public function vehicleDetails(Request $request)
    {
        $vehicleId = $request->vehicle_id;
        $language = $request->query('language');

        if (!$vehicleId) {
            return $this->error([], 'Vehicle ID is required', 400);
        }
        $withTranslation = function ($q) use ($language) {
            if ($language) {
                $q->with(['translations' => function ($t) use ($language) {
                    $t->where('language', $language);
                }]);
            }
        };

        $vehicle = Vehicle::with([
            'data.condition' => $withTranslation,
            'data.bodyColor' => $withTranslation,
            'data.upholstery' => $withTranslation,
            'data.interiorColor' => $withTranslation,
            'engineAndEnvironment.driverType' => $withTranslation,
            'engineAndEnvironment.transmission' => $withTranslation,
            'engineAndEnvironment.emissionClass' => $withTranslation,
            'category' => $withTranslation,
            'brand' => $withTranslation,
            'model' => $withTranslation,
            'fuel' => $withTranslation,
            'body_type' => $withTranslation,
            'transmission' => $withTranslation,
            'equipment_line' => $withTranslation,
            'seller_type' => $withTranslation,
            'currency', 'baseCurrency', 'user'
        ])->find($vehicleId);

        if (!$vehicle) {
            return $this->error([], 'Vehicle not found', 404);
        }

        if ($language) {
            $this->mapTranslations($vehicle, ['category','brand', 'model', 'fuel', 'body_type', 'transmission', 'equipment_line', 'seller_type']);
            if ($vehicle->data) {
                $this->mapTranslations($vehicle->data, ['condition', 'bodyColor', 'upholstery', 'interiorColor']);
            }
            if ($vehicle->engineAndEnvironment) {
                $this->mapTranslations($vehicle->engineAndEnvironment, ['driverType', 'transmission', 'emissionClass']);
            }
            $eqs = $vehicle->equipments;
            foreach ($eqs as $equipment) {
                $trans = $equipment->translations()->where('language', $language)->first();
                if ($trans) {
                    $equipment->title = $trans->title ?? $trans->name ?? $equipment->title;
                }
                $equipment->makeHidden(['translations', 'name']);
            }

            $vehicle->setRelation('equipments', $eqs);
        }

        return $this->success($vehicle, 'Vehicle details fetched successfully!', 200);
    }
    private function mapTranslations($model, $relations)
    {
        foreach ($relations as $rel) {
            if ($model->$rel) {
                // Check korchi translation ache kina
                if ($model->$rel->translations && $model->$rel->translations->isNotEmpty()) {
                    $translation = $model->$rel->translations->first();

                    // Priority: translation->title > translation->name > original->title
                    $model->$rel->title = $translation->title ?? $translation->name ?? $model->$rel->title;
                }

                // Response clean rakhar jonno translations ebong name field hide kore dewa
                $model->$rel->makeHidden(['translations', 'name']);
            }
        }
    }
    // getPendingAndAllVehicle
    public function getPendingAndAllVehicle(Request $request)
    {
        $lang = $request->query('language');

        // Translation load logic
        $withTr = function ($q) use ($lang) {
            if ($lang) {
                $q->with(['translations' => fn ($t) => $t->where('language', $lang)]);
            }
        };

        $vehicles = Vehicle::with([
            'category' => $withTr,
            'brand' => $withTr,
            'model' => $withTr,
            'subModel' => $withTr,
            'power', 'photos', 'currency', 'baseCurrency'
        ])
        ->whereIn('status', [0, 1])
        ->latest()
        ->paginate(20);

        $baseCur = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewCur = auth()->user()?->country?->currency ?? $baseCur;

        $vehicles->getCollection()->transform(function ($v) use ($baseCur, $viewCur, $lang) {

            if ($lang) {
                $this->mapTranslations($v, ['category', 'brand', 'model', 'subModel']);
            }
            $eqs = $v->equipments;
            if ($eqs && $eqs->isNotEmpty()) {
                foreach ($eqs as $equipment) {
                    if ($lang) {
                        $trans = $equipment->translations()->where('language', $lang)->first();
                        if ($trans) {
                            $equipment->title = $trans->title ?? $trans->name ?? $equipment->title;
                        }
                    }
                    $equipment->makeHidden(['translations', 'name']);
                }
                $v->setRelation('equipments', $eqs);
            }
            $v->poster_price = number_format($v->price, 2);
            $v->base_price = number_format($v->price_in_base, 2);
            $v->viewer_price = number_format(convertPrice($v->price_in_base, $baseCur->code, $viewCur->code), 2);
            $v->viewer_currency_symbol = $viewCur->symbol;

            return $v;
        });

        return $this->success($vehicles, 'Pending and active vehicles fetched successfully', 200);
    }
    public function featured(Request $request)
    {
        $lang = $request->query('language');

        // Translation helper logic
        $withTr = function ($q) use ($lang) {
            if ($lang) {
                $q->with(['translations' => fn ($t) => $t->where('language', $lang)]);
            }
        };

        // Featured vehicles fetch kora
        $featuredVehicles = Vehicle::with([
            'category' => $withTr,
            'brand' => $withTr,
            'model' => $withTr,
            'subModel' => $withTr,
            'power', 'photos', 'currency', 'baseCurrency'
        ])
        ->where('is_featured', 1)
        ->where('status', 1) // Active vehicles only
        ->latest()
        ->get(); // Collection fetch kora

        $baseCur = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewCur = auth()->user()?->country?->currency ?? $baseCur;

        $featuredVehicles->transform(function ($v) use ($baseCur, $viewCur, $lang) {

            // 1. Map Translations for Main Relations
            if ($lang) {
                $this->mapTranslations($v, ['category', 'brand', 'model', 'subModel']);
            }

            // 2. Equipments Mapping (Manual approach to ensure translation)
            $eqs = $v->equipments; // Accessor call
            if ($eqs && $eqs->isNotEmpty()) {
                foreach ($eqs as $equipment) {
                    if ($lang) {
                        // Direct query jate lazy loading translation miss na hoy
                        $trans = $equipment->translations()->where('language', $lang)->first();
                        if ($trans) {
                            $equipment->title = $trans->title ?? $trans->name ?? $equipment->title;
                        }
                    }
                    $equipment->makeHidden(['translations', 'name']);
                }
                $v->setRelation('equipments', $eqs);
            }

            // 3. Price Formatting & Currency
            $v->poster_price = number_format($v->price, 2);
            $v->base_price = number_format($v->price_in_base, 2);
            $v->viewer_price = number_format(convertPrice($v->price_in_base, $baseCur->code, $viewCur->code), 2);
            $v->viewer_currency_symbol = $viewCur->symbol;

            return $v;
        });

        return $this->success($featuredVehicles, 'Featured vehicles fetched successfully', 200);
    }

    public function downloadVehiclePdf($id)
    {
        $vehicle = Vehicle::with([
            'data.condition', 'photos', 'brand', 'model', 'fuel', 'transmission', 'power'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.vehicle', compact('vehicle'));
        return $pdf->download('vehicle-'.$vehicle->id.'.pdf');
    }
}
