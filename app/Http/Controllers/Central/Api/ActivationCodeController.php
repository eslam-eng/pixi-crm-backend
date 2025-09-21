<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivationCodeRequest;
use App\Http\Requests\UpdateActivationCodeRequest;
use App\Http\Resources\ActivationCodeCollection;
use App\Services\Central\ActivationCodeService;
use Illuminate\Http\Request;

class ActivationCodeController extends Controller
{
    public function __construct(
        public ActivationCodeService $activationCodeService
    ) {}

    public function get_statistics()
    {
        $statistics = $this->activationCodeService->statistics();
        return apiResponse($statistics, 'Statistics retrieved successfully.');
    }

    public function index(Request $request)
    {
        $filter['using_state'] = $request->query('using_state', 'all');
        $activationCodes = $this->activationCodeService->index(
            filters: $filter,
            withRelations: ['tier', 'createBy']
        );
        return apiResponse(new ActivationCodeCollection($activationCodes), 'Activation codes retrieved successfully.');
    }

    public function store(StoreActivationCodeRequest $request)
    {
        $data = $request->validated();
        $message = $this->activationCodeService->store($data);
        return apiResponse($message, 'Operation completed successfully.');
    }

    public function update(UpdateActivationCodeRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['id'] = $id;
            $message = $this->activationCodeService->update($data);
            return apiResponse($message, 'Operation completed successfully.');
        } catch (\Exception $e) {
            return apiResponse($e->getMessage(), 'update failed.', 500);
        }
    }

    public function show($id, array $withRelations = ['tier'])
    {
        $activationCode = $this->activationCodeService->show($id, $withRelations);
        return apiResponse($activationCode, 'Activation code retrieved successfully.');
    }

    public function destroy($id)
    {
        $message = $this->activationCodeService->destroy($id);
        return apiResponse($message, 'Operation completed successfully.');
    }
}
