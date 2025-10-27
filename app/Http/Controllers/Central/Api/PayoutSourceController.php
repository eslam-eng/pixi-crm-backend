<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\SourcePayoutBatchDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\CreateCollectionRequest;
use App\Http\Resources\Central\SourcePayoutCollectionResource;
use App\Services\Central\ActivationCode\SourcePayoutBatchService;
use Illuminate\Http\Request;

class PayoutSourceController extends Controller
{
    public function __construct(private readonly SourcePayoutBatchService $sourcePayoutBatchService) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);
        $collections = $this->sourcePayoutBatchService->paginate(filters: $filters, perPage: $limit);

        return SourcePayoutCollectionResource::collection($collections);
    }

    public function createCollection(CreateCollectionRequest $request)
    {
        $createCollectionDTO = SourcePayoutBatchDTO::fromRequest($request);
        $this->sourcePayoutBatchService->create(sourcePayoutBatchDTO: $createCollectionDTO);

        return ApiResponse::success(message: 'collection created successfully');
    }

    public function details(int $collection_id)
    {
        $collection = $this->sourcePayoutBatchService->getPayoutItems($collection_id);

        return SourcePayoutCollectionResource::make($collection);
    }

    public function markCollected($payout_source_id)
    {
        $this->sourcePayoutBatchService->markCollected($payout_source_id);

        return ApiResponse::success(message: 'payout source marked as collected successfully');
    }

    public function collectedSpaceficPayoutItem($payout_source_id, Request $request)
    {
        $payout_items_ids = $request->get('payout_items_ids', []);
        if (empty($payout_items_ids)) {
            return ApiResponse::error(message: 'payout items ids is required at least one item');
        }

        $this->sourcePayoutBatchService->collectSpaceficCodes(source_payout_batch_id: $payout_source_id, payout_item_ids: $payout_items_ids);

        return ApiResponse::success(message: 'selected payout items marked as collected successfully');
    }
}
