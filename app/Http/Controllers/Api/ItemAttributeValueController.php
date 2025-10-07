<?php
// app/Http/Controllers/AttributeValueController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\GeneralException;
use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use App\Http\Requests\Item\Attribute\CreateAttributeValueRequest;
use App\Http\Resources\Tenant\Items\Attribute\ItemAttributeValueResource;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Response;

class ItemAttributeValueController extends Controller
{
    public function __construct()
    {
    }

    public function store(CreateAttributeValueRequest $request, ItemAttribute $attribute)
    {
        $attributeValue = $attribute->values()->create($request->validated());
        return ApiResponse(message: 'Attribute value created successfully', data: new ItemAttributeValueResource($attributeValue), code: Response::HTTP_CREATED);
    }

    public function update(CreateAttributeValueRequest $request, ItemAttribute $attribute, ItemAttributeValue $value)
    {
        $value->update($request->validated());
        return ApiResponse(message: 'Attribute value updated successfully', data: new ItemAttributeValueResource($value), code: Response::HTTP_OK);
    }

    public function destroy(ItemAttribute $attribute, ItemAttributeValue $value)
    {
        try {
            DB::beginTransaction();
            if ($value->variants()->exists()) {
                throw new GeneralException(__('app.cannot_delete_item_attribute_value_used_by_variants'));
            }
            $value->delete();
            DB::commit();
            return ApiResponse(message: 'Attribute value deleted successfully', code: Response::HTTP_OK);
        } catch (GeneralException $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
