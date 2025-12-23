<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ExportContactRequest extends FormRequest
{
    public function rules(): array
    {
        $validColumns = Schema::getColumnListing('contacts');

        return [
            'columns' => 'required|array|min:1', // e.g., ['first_name', 'email']
            'columns.*' => ['string', Rule::in($validColumns)]
        ];
    }
}
