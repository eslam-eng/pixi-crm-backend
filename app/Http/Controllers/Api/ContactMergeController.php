<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\DTO\Contact\ContactMergeDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\ContactMerge\ContactMergeResource;
use App\Services\ContactMergeService;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class ContactMergeController extends Controller
{
    public function __construct(public ContactMergeService $contactMergeService)
    {
        $this->middleware('permission:manage-settings');
    }

    public function form(Request $request)
    {
        try {
            $contactMergeDTO = ContactMergeDTO::fromRequest($request);
            DB::beginTransaction();
            $result = $this->contactMergeService->handleForm($contactMergeDTO);
            DB::commit();
            if ($result) {
                return ApiResponse(message: 'new contact created successfully');
            } else {
                return ApiResponse(message: 'duplicate contact created successfully');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function mergeList()
    {
        $contactMerge = $this->contactMergeService->mergeList();
        return ApiResponse(message: 'contact merge list', data: ContactMergeResource::collection($contactMerge));
    }

    public function merge()
    {
        try {
            DB::beginTransaction();
            $errors = $this->contactMergeService->handleMerge();
            DB::commit();
            if ($errors) {
                return ApiResponse(message: 'Please fix the following errors', data: $errors, code: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$errors) {
                return ApiResponse(message: 'contact merged successfully', data: $errors);
            } else {
                return ApiResponse(message: 'you dont have any pending merge', code: Response::HTTP_NOT_FOUND);
            }
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'merge contact not found', code: Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function mergeById($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->contactMergeService->handleMergeById($id);
            DB::commit();
            if ($result) {
                return ApiResponse(message: 'contact merged successfully');
            }
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'merge contact not found', code: Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function ignoreById($id)
    {
        try {
            DB::beginTransaction();
            $this->contactMergeService->handleIgnoreById($id);
            DB::commit();
            return ApiResponse(message: 'Merge contact ignored successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'merge contact not found', code: Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function ignore()
    {
        $result = $this->contactMergeService->handleIgnore();
        if ($result) {
            return ApiResponse(message: 'Merge contacts ignored successfully');
        } else {
            return ApiResponse(message: 'Merge contacts not ignored', code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
