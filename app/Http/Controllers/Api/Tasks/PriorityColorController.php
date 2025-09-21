<?php

namespace App\Http\Controllers\Api\Tasks;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Tasks\PriorityColorResource;
use App\Services\Tenant\Tasks\PriorityColorService;

class PriorityColorController extends Controller
{
    public function __construct(public PriorityColorService $priorityColorService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $colors = $this->priorityColorService->getAll([]);
            $data = PriorityColorResource::collection($colors);
            return apiResponse( $data, trans('app.data displayed successfully'));
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

}