<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ItemStatus;
use DB;
use Exception;
use Illuminate\Http\Request;

class ItemStatusController extends Controller
{

    public function index(Request $request)
    {
        $query = ItemStatus::query();

        $itemStatuses = $query->paginate($request->per_page ?? 10);
        return ApiResponse(message: 'Item statuses retrieved successfully', code: 200, data: $itemStatuses);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate(
                [
                    'name' => 'required|string|max:255|unique:item_statuses,name',
                    'status' => 'required|boolean',
                ]
            );
            ItemStatus::create($data);
            DB::commit();
            return ApiResponse(message: 'Item status created successfully', code: 201);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
