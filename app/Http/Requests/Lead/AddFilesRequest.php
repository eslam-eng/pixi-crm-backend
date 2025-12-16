<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class AddFilesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => 'requiredIf:documents,null|array',
            'images.*'  => 'required|string|max:255',
            'documents' => 'requiredIf:images,null|array',
            'documents.*'  => 'required|string|max:255',
            'category'  => 'required|in:proposal,contract,presentation,invoice,other',
            'description'  => 'nullable|string',
        ];
    }
}
