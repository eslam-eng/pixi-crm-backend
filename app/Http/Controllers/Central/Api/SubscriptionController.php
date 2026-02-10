<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Subscription\StoreSubscriptionRequest;
use App\Mail\Central\SubscriptionActivated;
use App\Services\Central\SubscriptionService;
use App\Http\Resources\Central\SubscriptionResource;
use App\Http\Resources\Central\SubscriptionShowResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

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
            message: 'Subscriptions retrieved successfully',
            data: SubscriptionResource::collection($subscriptions)->response()->getData(true)
        );
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptionService->store(
            $request->validated(),
            $request->file('file')
        );

        if ($subscription->tenant && $subscription->tenant->owner) {
            Mail::to($subscription->tenant->owner->email)
                ->send(new SubscriptionActivated($subscription));
        }

        return apiResponse(
            message: 'Subscription created successfully',
            data: new SubscriptionResource($subscription)
        );
    }

    public function show(string $id): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->findById($id, [
                'tenant',
                'activationCode.source',
                'source',
                'plan',
                'featureSubscriptions',
                'invoices'
            ]);

            return apiResponse(
                message: 'Subscription retrieved successfully',
                data: new SubscriptionShowResource($subscription)
            );
        } catch (NotFoundHttpException $e) {
            return apiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function renew(string $id): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->renewManual($id);

            return apiResponse(
                message: 'Subscription renewed successfully',
                data: new SubscriptionResource($subscription)
            );
        } catch (NotFoundHttpException $e) {
            return apiResponse(message: $e->getMessage(), code: 404);
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function statics(): JsonResponse
    {
        $statics = $this->subscriptionService->statics();

        return apiResponse(
            data: $statics,
            message: 'Subscription statics retrieved successfully'
        );
    }
}