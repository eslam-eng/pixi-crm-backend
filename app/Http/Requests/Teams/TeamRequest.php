<?php

namespace App\Http\Requests\Teams;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TeamRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guard = config('auth.defaults.guard', 'web');

        $roleIds = Role::query()
            ->where('guard_name', $guard)
            ->whereIn('name', ['admin', 'manager', 'agent'])
            ->pluck('id', 'name'); // ['admin'=>1,'manager'=>2,'sales'=>3]

        $teamIdForUnique = $this->route('team');
        return [
            'title' => ['required', 'string', Rule::unique('teams', 'title')->ignore($teamIdForUnique), 'max:255'],
            'leader_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
                Rule::exists('model_has_roles', 'model_id')
                    ->whereIn('role_id', [$roleIds['admin'] ?? 0, $roleIds['manager'] ?? 0])
                    ->where('model_type', 'user'),
            ],
            'sales_ids' => ['nullable', 'array'],
            'sales_ids.*' => [
                'nullable',
                'distinct',
                'integer',
                Rule::exists('users', 'id')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'leader_id.exists' => 'Selected team leader must be an existing user.',
            'leader_id.*model_has_roles*' => 'Leader must have role: admin or manager.',
            'sales_ids.*.exists' => 'Each salesperson must be an existing user.',
            'sales_ids.*.distinct' => 'Duplicate salesperson selected.',
            'sales_ids.*.not_in' => 'Leader cannot be added as a salesperson.',
        ];
    }
}
