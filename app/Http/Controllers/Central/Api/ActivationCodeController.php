<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\ActivationCodeDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ActivationCodeRequest;
use App\Http\Resources\Central\ActivationCodeResource;
use App\Models\Central\ActivationCode;
use App\Services\Central\ActivationCode\ActivationCodeService;
use Illuminate\Http\Request;

class ActivationCodeController extends Controller
{
    public function __construct(private readonly ActivationCodeService $activationCodeService) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);
        $activationCodes = $this->activationCodeService->paginate(filters: $filters, perPage: $limit);

        return ActivationCodeResource::collection($activationCodes);
    }

    public function store(ActivationCodeRequest $request)
    {
        $activationCodeDTO = ActivationCodeDTO::fromRequest($request);
        $this->activationCodeService->generate($activationCodeDTO);

        return ApiResponse::success(message: 'Activation codes generated successfully');
    }

    public function delete(ActivationCode|int $activation_code)
    {
        $this->activationCodeService->delete($activation_code);

        return ApiResponse::success(message: 'Activation code deleted successfully');
    }

    public function statics()
    {
        $statics = $this->activationCodeService->statics();
        $statics = array_map('intval', $statics->toArray());

        return ApiResponse::success(data: $statics);
    }
}
