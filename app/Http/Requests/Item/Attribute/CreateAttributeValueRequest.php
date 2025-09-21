<?php

namespace App\Http\Requests\Item\Attribute;

use App\Http\Requests\BaseRequest;

class CreateAttributeValueRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'value' => [
                'required',
                'string',
                'max:255',
                'unique:item_attribute_values,value,NULL,id,item_attribute_id,' . $this->route('attribute')->id
            ],
        ];
    }

    public function messages()
    {
        return [
            'value.unique' => 'This value already exists for this attribute.',
        ];
    }
}
