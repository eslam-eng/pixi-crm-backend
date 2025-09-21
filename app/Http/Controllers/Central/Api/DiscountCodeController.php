<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivationCodeRequest;
use App\Http\Resources\ActivationCodeCollection;
use App\Services\Central\DiscountCodeService;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function __construct(
        public DiscountCodeService $discountCodeService
    ) {}

    public function index(Request $request)
    {
        $filter['using_state'] = $request->query('using_state', 'all');
        $activationCodes = $this->discountCodeService->index(
            filters: $filter,
            withRelations: ['tier', 'createBy']
        );
        return apiResponse(new ActivationCodeCollection($activationCodes), 'created codes retrieved successfully.');
    }

    public function store(StoreActivationCodeRequest $request)
    {
        $data = $request->validated();
        $message = $this->discountCodeService->store($data);
        return apiResponse($message, 'Operation completed successfully.');
    }
}
