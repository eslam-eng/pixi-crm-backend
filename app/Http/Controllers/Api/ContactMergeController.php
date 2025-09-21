<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\DTO\Contact\ContactMergeDTO;
use App\Http\Controllers\Controller;
use App\Services\ContactMergeService;
use DB;
use Exception;
use Illuminate\Http\Response;

class ContactMergeController extends Controller
{
    public function __construct(public ContactMergeService $contactMergeService) {}

    public function form(Request $request)
    {
        try {
            $contactMergeDTO = ContactMergeDTO::fromRequest($request);
            DB::beginTransaction();
            $result = $this->contactMergeService->handleForm($contactMergeDTO);
            DB::commit();
            if ($result) {
                return ApiResponse(message: 'duplicate contact created successfully');
            } else {
                return ApiResponse(message: 'new contact created successfully');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function mergeList()
    {
        $contactMerge = $this->contactMergeService->mergeList();
        return ApiResponse(message: 'contact merge list', data: $contactMerge);
    }

    public function merge()
    {
        try {
            DB::beginTransaction();
            $result = $this->contactMergeService->handleMerge();
            DB::commit();
            if ($result) {
                return ApiResponse(message: 'contact merged successfully');
            } else {
                return ApiResponse(message: 'contact not merged');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function ignore()
    {
        $result = $this->contactMergeService->handleIgnore();
        if ($result) {
            return ApiResponse(message: 'contact ignored successfully');
        } else {
            return ApiResponse(message: 'contact not ignored', code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
