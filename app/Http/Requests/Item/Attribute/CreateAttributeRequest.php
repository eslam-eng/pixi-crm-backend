<?php

namespace App\Http\Requests\Item\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttributeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:item_attributes,name',
        ];
    }
}
