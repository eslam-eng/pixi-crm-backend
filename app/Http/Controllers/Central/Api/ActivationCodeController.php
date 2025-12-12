<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\ActivationCodeDTO;
use App\Exports\ActivationCodesExport;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ActivationCodeIndexRequest;
use App\Http\Requests\Central\ActivationCodeRequest;
use App\Http\Requests\Central\MultiDeleteActivationCodeRequest;
use App\Http\Requests\Central\ExportCodesRequest;
use App\Http\Requests\Central\UpdateActivationCodeStatusRequest;
use App\Http\Resources\Central\ActivationCodeResource;
use App\Models\Central\ActivationCode;
use App\Services\Central\ActivationCode\ActivationCodeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ActivationCodeController extends Controller
{
    public function __construct(private readonly ActivationCodeService $activationCodeService)
    {
    }

    public function index(ActivationCodeIndexRequest $request)
    {
        $filters = $request->validated();
        $limit = $request->input('limit', 15);
        $activationCodes = $this->activationCodeService->paginate(filters: $filters, perPage: $limit);

        $data = ActivationCodeResource::collection($activationCodes)->response()->getData(true);
        return ApiResponse::success(data: $data, message: 'Activation codes generated successfully');

    }

    public function store(ActivationCodeRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by_id'] = auth()->user()->id;

        $activationCodeDTO = ActivationCodeDTO::fromArray($inputs);

        $codes = $this->activationCodeService->store($activationCodeDTO);

        return Excel::download(new ActivationCodesExport($codes), 'activation_codes.xlsx');
    }
    public function storeValidation(ActivationCodeRequest $request)
    {
        $request->validated();
        return apiResponse(message: 'Validation Passed successfully', code: 200);

    }
    public function generateCode()
    {
        $code = $this->activationCodeService->generateCode();

        return ApiResponse::success(data: $code);
    }

    public function delete(ActivationCode|string|int $activation_code)
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
    public function exportCodes(ExportCodesRequest $request)
    {
        $inputs = $request->validated();
        $codes = $this->activationCodeService->exportCodesRequest(inputs: $inputs);

        return Excel::download(new ActivationCodesExport($codes), 'activation_codes.xlsx');
    }

    public function multiUpdateStatus(UpdateActivationCodeStatusRequest $request)
    {
        $inputs = $request->validated();

        $this->activationCodeService->updateStatus(inputs: $inputs);

        return ApiResponse::success(message: 'Activation codes status updated successfully');
    }

    public function multiDelete(MultiDeleteActivationCodeRequest $request)
    {
        $this->activationCodeService->deleteMulti($request->validated()['ids']);

        return ApiResponse::success(message: 'Activation codes deleted successfully');
    }
}
