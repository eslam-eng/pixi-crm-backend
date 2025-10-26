<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LocationCollection;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LocationService;

class LocationController extends Controller
{
    public function __construct(public LocationService $locationService) {}

    public function getCountries(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['cities'];
            $filters = [];

            $locations = $this->locationService->getCountries($filters, $withRelations, $perPage);
            return ApiResponse(new LocationCollection($locations), 'Countries retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getMessage());
        }
    }

    public function getCities(Request $request, $country_id): \Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['cities'];
            $locations = $this->locationService->getCities($country_id, $filters, $withRelations, $perPage);
            return ApiResponse(new LocationCollection($locations), 'Cities retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function getAreas(Request $request, $city_id): \Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = [];
            $locations = $this->locationService->getAreas($city_id, $filters, $withRelations, $perPage);
            return ApiResponse(new LocationCollection($locations), 'areas retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
