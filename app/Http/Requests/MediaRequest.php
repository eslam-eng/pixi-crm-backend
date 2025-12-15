<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class MediaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*.name' => ['required', 'string', 'max:255'],
            'images.*.file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'documents' => ['nullable' , 'array','max:2048'],
            'documents.*.name' => ['required', 'string', 'max:255'],
            'documents.*.file' => ['required', 'file', 'max:2048'],
        ];
    }

}
