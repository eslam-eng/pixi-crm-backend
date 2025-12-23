<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Subscription\StoreSubscriptionRequest;
use App\Services\Central\SubscriptionService;
use App\Http\Resources\Central\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $subscriptions = $this->subscriptionService->getQuery(
            filters: $request->all(),
            withRelation: ['tenant', 'activationCode.source', 'source']
        )->latest()->paginate($request->get('per_page', 15));

        return apiResponse(
            data: SubscriptionResource::collection($subscriptions)->response()->getData(true)
        );
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptionService->store(
            $request->validated(),
            $request->file('file')
        );

        return apiResponse(
            message: 'Subscription created successfully',
            data: new SubscriptionResource($subscription)
        );
    }

    public function renew(string $id): JsonResponse
    {
        $subscription = $this->subscriptionService->renewManual($id);

        return apiResponse(
            message: 'Subscription renewed successfully',
            data: new SubscriptionResource($subscription)
        );
    }
}
