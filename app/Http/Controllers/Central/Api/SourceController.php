<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\SourceDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\SourceRequest;
use App\Http\Resources\Central\SourceResource;
use App\Services\Central\SourceService;
use Illuminate\Http\Request;

/**
 * source refers to external source like evanto,themforest, AppSomos, etc.
 */
class SourceController extends Controller
{
    public function __construct(protected SourceService $sourceService) {}

    /**
     * Display a listing of the sources.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $sources = $this->sourceService->list($filters);

        return SourceResource::collection($sources);
    }

    /**
     * Store a newly created source in storage.
     */
    public function store(SourceRequest $request)
    {
        $dto = SourceDTO::fromRequest($request);
        $source = $this->sourceService->create($dto);

        return ApiResponse::success(
            message: 'Source created successfully.'
        );
    }

    /**
     * Display the specified source.
     */
    public function show(int $id)
    {
        $source = $this->sourceService->findById($id);

        return ApiResponse::success(
            data: SourceResource::make($source)
        );
    }

    /**
     * Update the specified source in storage.
     */
    public function update(SourceRequest $request, int $id)
    {
        $dto = SourceDTO::fromRequest($request);
        $this->sourceService->update($id, $dto);

        return ApiResponse::success(
            message: 'Source updated successfully.'
        );
    }

    /**
     * Remove the specified source from storage.
     *
     * @throws \App\Exceptions\CannotDeleteResourceException
     */
    public function destroy(int $id)
    {
        try {
            $this->sourceService->delete($id);

            return ApiResponse::success(
                message: 'Source deleted successfully.'
            );
        } catch (\App\Exceptions\CannotDeleteResourceException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: $e->getCode()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                message: 'An error occurred while deleting the source.',
                code: 500
            );
        }
    }

    /**
     * Toggle the active status of the specified source.
     */
    public function toggleStatus(int $id)
    {
        $this->sourceService->toggleStatus($id);

        return ApiResponse::success(
            message: 'Source status updated successfully.'
        );
    }
}
