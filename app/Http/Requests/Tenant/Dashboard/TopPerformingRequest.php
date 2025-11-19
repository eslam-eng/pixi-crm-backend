<?php

namespace App\Http\Requests\Tenant\Dashboard;

use App\Enums\PermissionsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TopPerformingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !Auth::user()->hasPermissionTo(PermissionsEnum::VIEW_AGENT_DASHBOARD->value);
    }

    public function rules(): array
    {
        return [
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ];
    }
}
