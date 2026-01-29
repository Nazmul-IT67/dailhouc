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

        $withTranslation = function($q) use ($language) {
            if ($language) {
                $q->with(['translations' => fn($t) => $t->where('language', $language)]);
            }
        };

        $vehicles = Vehicle::select(
            'vehicles.id', 'vehicles.category_id', 'vehicles.brand_id', 'vehicles.model_id',
            'vehicles.body_type_id', 'vehicles.sub_model_id', 'vehicles.power_id',
            'vehicles.price', 'vehicles.price_in_base', 'vehicles.currency_id',
            'vehicles.base_currency_id', 'vehicles.first_registration'
        )
        ->with([
            'power', 'category' => $withTranslation, 'brand' => $withTranslation,  'model' => $withTranslation, 'subModel' => $withTranslation, 'body_type' => $withTranslation, 'data.condition' => $withTranslation, 'photos', 'contactInfo', 'contactInfo.country', 'contactInfo.city', 'currency', 'baseCurrency'
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

    // getUsersVehicle
    public function getUsersVehicle(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthenticated. Please log in to access your vehicles.', 200);
        }

        $language = $request->query('language');

        $withTranslation = function($q) use ($language) {
            if ($language) {
                $q->with(['translations' => fn($t) => $t->where('language', $language)]);
            }
        };

        $vehicles = Vehicle::select(
            'vehicles.id', 'vehicles.category_id', 'vehicles.brand_id', 'vehicles.model_id',
            'vehicles.body_type_id', 'vehicles.sub_model_id', 'vehicles.power_id',
            'vehicles.price', 'vehicles.price_in_base', 'vehicles.currency_id',
            'vehicles.base_currency_id', 'vehicles.first_registration', 'vehicles.user_id'
        )
        ->with([
            'power',
            'category'  => $withTranslation,
            'brand'     => $withTranslation,
            'model'     => $withTranslation,
            'body_type' => $withTranslation,
            'subModel'  => $withTranslation,
            'photos', 'contactInfo', 'data.condition', 'currency', 'baseCurrency'
        ])
        ->where('status', 1)
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $baseCurrency = Currency::where('is_default', 1)->first();
        if (!$baseCurrency) return $this->error([], 'Base currency is not set!', 500);

        $viewerCurrency = $request->user()?->country?->currency ?? $baseCurrency;

            $vehicles->getCollection()->transform(function ($v) use ($language, $baseCurrency, $viewerCurrency)
            {
                $relations = ['category', 'brand', 'model', 'body_type', 'subModel'];
                
                if ($language) {
                foreach ($relations as $rel) {
                    if ($v->$rel && $v->$rel->translations->isNotEmpty()) {
                        $tr = $v->$rel->translations->first();
                        
                        $translatedValue = $tr->name ?? $tr->title;
                        $v->$rel->name = $translatedValue;
                        if (isset($v->$rel->title)) {
                            $v->$rel->title = $translatedValue;
                        }
                    }
                    if ($v->$rel) $v->$rel->makeHidden('translations');
                }
            }

            if ($v->data) $v->data->bed_types = $v->data->bedTypes;

            $v->poster_price = number_format($v->price, 2);
            $v->poster_currency_symbol = $v->currency?->symbol ?? '';

            $v->base_price = number_format($v->price_in_base, 2);
            $v->base_currency_symbol = $v->baseCurrency?->symbol ?? '$';

            $v->viewer_price = number_format(
                convertPrice($v->price_in_base, $baseCurrency->code, $viewerCurrency->code), 2
            );
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

        $withTranslation = function($q) use ($language) {
            if ($language) {
                $q->with(['translations' => fn($t) => $t->where('language', $language)]);
            }
        };

        $vehicle = Vehicle::with([
            'data.condition' => $withTranslation,
            'data.bodyColor' => $withTranslation,
            'data.upholstery' => $withTranslation,
            'data.interiorColor' => $withTranslation,
            'data.previousOwner', 'data.numOfDoor', 'data.numOfSeats', 'data.bedCount',
            'photos', 'power',
            'engineAndEnvironment.driverType' => $withTranslation,
            'engineAndEnvironment.transmission' => $withTranslation,
            'engineAndEnvironment.emissionClass' => $withTranslation,
            'conditionAndMaintenance', 'contactInfo',
            'category' => $withTranslation,
            'brand' => $withTranslation,
            'model' => $withTranslation,
            'fuel' => $withTranslation,
            'body_type' => $withTranslation,
            'transmission' => $withTranslation,
            'equipment_line' => $withTranslation,
            'seller_type' => $withTranslation,
            'currency', 'baseCurrency', 'fuel'
        ])->find($vehicleId);

        if (!$vehicle) {
            return $this->error([], 'Vehicle not found', 404);
        }

        if ($language) {
            $mainRelations = ['category', 'brand', 'model', 'fuel', 'body_type', 'transmission', 'equipment_line', 'seller_type'];
            foreach ($mainRelations as $rel) {
                if ($vehicle->$rel && $vehicle->$rel->translations->isNotEmpty()) {
                    $vehicle->$rel->name = $vehicle->$rel->translations->first()->name;
                    $vehicle->$rel->makeHidden('translations');
                }
            }

            if ($vehicle->data) {
                $dataRelations = ['condition', 'bodyColor', 'upholstery', 'interiorColor'];
                foreach ($dataRelations as $rel) {
                    if ($vehicle->data->$rel && $vehicle->data->$rel->translations->isNotEmpty()) {
                        $vehicle->data->$rel->name = $vehicle->data->$rel->translations->first()->name;
                        $vehicle->data->$rel->makeHidden('translations');
                    }
                }
                $vehicle->data->bed_types = $vehicle->data->bedTypes;
            }

            if ($vehicle->engineAndEnvironment) {
                $engRelations = ['driverType', 'transmission', 'emissionClass'];
                foreach ($engRelations as $rel) {
                    if ($vehicle->engineAndEnvironment->$rel && $vehicle->engineAndEnvironment->$rel->translations->isNotEmpty()) {
                        $vehicle->engineAndEnvironment->$rel->name = $vehicle->engineAndEnvironment->$rel->translations->first()->name;
                        $vehicle->engineAndEnvironment->$rel->makeHidden('translations');
                    }
                }
            }
        }

        $baseCurrency = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewerCurrency = auth()->user()?->country?->currency ?? $baseCurrency;

        $vehicle->poster_price = number_format($vehicle->price, 2);
        $vehicle->poster_currency_symbol = $vehicle->currency?->symbol ?? '';
        $vehicle->base_price = number_format($vehicle->price_in_base, 2);
        $vehicle->base_currency_symbol = $vehicle->baseCurrency?->symbol ?? '$';
        $vehicle->viewer_price = number_format(convertPrice($vehicle->price_in_base, $baseCurrency->code, $viewerCurrency->code), 2);
        $vehicle->viewer_currency_symbol = $viewerCurrency->symbol;

        if ($vehicle->contactInfo) {
            $contact = $vehicle->contactInfo;
            $vehicle->contact_details = [
                'name' => $contact->name,
                'email' => $contact->is_email_show ? $vehicle->user?->email : null,
                'phone' => $contact->is_number_show ? $vehicle->user?->phone : null,
                'avatar' => $contact->avatar ?? null,
                'whatsapp_number' => $contact->is_whatsapp_show ? $contact->whatsapp_number : null,
                'whatsapp_country_code' => $contact->is_whatsapp_show ? $contact->whatsapp_country_code : null,
                'country' => $contact->country?->name,
                'city' => $contact->city?->name,
                'postal_code' => $contact->postal_code,
                'street_details' => $contact->street_details,
                'lat' => $contact->lat,
                'lng' => $contact->lng,
            ];
            $vehicle->makeHidden('contactInfo'); 
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $vehicle->pdf_download_url = "{$baseUrl}/vehicle/download-pdf/{$vehicle->id}";

        return $this->success($vehicle, 'Vehicle details fetched successfully!', 200);
    }
    
    // downloadVehiclePdf
    public function downloadVehiclePdf($id)
    {
        $vehicle = Vehicle::with([
            'data.condition', 'photos', 'brand', 'model', 'fuel', 'transmission', 'power'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.vehicle', compact('vehicle'));
        return $pdf->download('vehicle-'.$vehicle->id.'.pdf');
    }

    // getPendingAndAllVehicle
    public function getPendingAndAllVehicle(Request $request)
    {
        $user = auth('sanctum')->user();
        $lang = $request->query('language');
        $withTr = fn($q) => $lang ? $q->with(['translations' => fn($t) => $t->where('language', $lang)]) : null;

        $vehicles = Vehicle::with([
            'category' => $withTr, 'brand' => $withTr, 'model' => $withTr,
            'subModel' => $withTr, 'power', 'photos', 'currency', 'baseCurrency', 'contactInfo', 'contactInfo.country', 'contactInfo.city', 'transmission', 'data.condition', 'fuel'
        ])
        ->withExists(['favoritedBy as is_favorite' => function($q) use ($user) {
            $q->where('user_id', $user?->id);
        }])
        ->whereIn('status', [0, 1])
        ->latest()
        ->paginate(20);

        $baseCur = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewCur = auth()->user()?->country?->currency ?? $baseCur;

        $vehicles->getCollection()->transform(function ($v) use ($baseCur, $viewCur, $lang) {
            if ($lang) {
                foreach (['category', 'brand', 'model', 'subModel'] as $rel) {
                    if ($v->$rel && $v->$rel->translations->isNotEmpty()) {
                        $tr = $v->$rel->translations->first();
                        $v->$rel->title = $tr->title ?? $tr->name;
                        $v->$rel->name = $tr->name ?? $tr->title;
                        $v->$rel->makeHidden('translations');
                    }
                }
            }

            if ($v->equipment_ids) {
                $ids = is_array($v->equipment_ids) ? $v->equipment_ids : explode(',', $v->equipment_ids);
                $v->equipments = \App\Models\Equipment::whereIn('id', $ids)
                    ->with(['translations' => fn($q) => $lang ? $q->where('language', $lang) : $q])
                    ->get()
                    ->map(function($eq) use ($lang) {
                        if ($lang && $eq->translations->isNotEmpty()) {
                            $eq->title = $eq->translations->first()->title ?? $eq->translations->first()->name;
                        }
                        return $eq->makeHidden('translations');
                    });
            }

            $v->poster_price = number_format($v->price, 2);
            $v->base_price = number_format($v->price_in_base, 2);
            $v->viewer_price = number_format(convertPrice($v->price_in_base, $baseCur->code, $viewCur->code), 2);
            $v->viewer_currency_symbol = $viewCur->symbol;

            return $v;
        });

        return $this->success($vehicles, 'Pending and active vehicles fetched successfully', 200);
    }

    // mapTranslations
    private function mapTranslations($vehicle, array $relations)
    {
        foreach ($relations as $rel) {
            if ($vehicle->{$rel} && $vehicle->{$rel}->translations && $vehicle->{$rel}->translations->isNotEmpty()) {
                
                $translation = $vehicle->{$rel}->translations->first();
                $vehicle->{$rel}->name = $translation->name ?? $translation->title ?? $vehicle->{$rel}->name;
                $vehicle->{$rel}->makeHidden('translations');
            }
        }
    }

    // getfeatured
    public function featured(Request $request)
    {
        $user = auth('sanctum')->user();
        $lang = $request->query('language');
        $withTr = function ($q) use ($lang) {
            if ($lang) {
                $q->with(['translations' => fn ($t) => $t->where('language', $lang)]);
            }
        };

        $featuredVehicles = Vehicle::with([
            'category' => $withTr,
            'brand' => $withTr,
            'model' => $withTr,
            'subModel' => $withTr,
            'power', 'photos', 'currency', 'baseCurrency', 'contactInfo', 'contactInfo.country', 'contactInfo.city', 'transmission', 'data.condition', 'fuel'
        ])
        ->withExists(['favoritedBy as is_favorite' => function($q) use ($user) {
            $q->where('user_id', $user?->id);
        }])
        ->where('is_featured', 1)
        ->where('status', 1)
        ->latest()
        ->get();

        $baseCur = Currency::where('is_default', 1)->first() ?? Currency::where('code', 'USD')->first();
        $viewCur = auth()->user()?->country?->currency ?? $baseCur;

        $featuredVehicles->transform(function ($v) use ($baseCur, $viewCur, $lang) {

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

        return $this->success($featuredVehicles, 'Featured vehicles fetched successfully', 200);
    }

}