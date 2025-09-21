<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\landloardLocation\CityResource;
use App\Http\Resources\landloardLocation\CountryResource;
use App\Models\City;
use App\Models\Country;
use Exception;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function getCountries(Request $request)
    {
        try {
            $countries = Country::all();
            return ApiResponse(CountryResource::collection($countries), 'Countries retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getCities(Request $request)
    {
        try {
            $cities = City::where('country_id', $request->countryId)->get();
            return ApiResponse(CityResource::collection($cities), 'Cities retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
