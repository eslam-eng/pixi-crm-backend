<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Dashboard\ActivityResource;
use App\Models\Tenant\Activity;

class ActivityController extends Controller
{
    public function __invoke()
    {
        $activities = Activity::visibleFor(user_id())
        ->with(['causer', 'subject'])
        ->latest()
        ->paginate(20);;

        $data = ActivityResource::collection($activities)->response()->getData(true);;
        
        return apiResponse($data, 'Activities fetched successfully', 200);
    }
}