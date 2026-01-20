<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class LocationController extends Controller
{
    use ApiResponse;
    public function getCountry()
    {
        $countries = Country::all();
        if (!$countries) {
            return $this->error([], 'Something went Wrong', 200);
        }
        return $this->success($countries, 'Countries  fetch Successful!', 200);
    }
    public function getCity(Request $request)
    {
        $query = City::with('country');
        if ($request->has('country_id') && !empty($request->country_id)) {
            $query->where('country_id', $request->country_id);
        }
        $cities = $query->get();

        if ($cities->isEmpty()) {
            return $this->error([], 'No cities found', 404);
        }

        return $this->success($cities, 'Cities fetched successfully!', 200);
    }
}
