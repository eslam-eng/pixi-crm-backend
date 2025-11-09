<?php

namespace App\Http\Requests\Tenant\Team;

use App\Http\Requests\BaseRequest;

class TeamBulkAssignRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'team_name' => 'required|string',
            'description' => 'nullable|string',
            'team_leader_id' => 'required|exists:users,id',
            'sales' => 'required|array',
            'sales.*' => 'required|exists:users,id|distinct:strict',
            'status' => 'required|string',
            'is_target' => 'required|boolean',
            'period_type' => 'requiredIf:is_target,true|string|in:monthly,quarterly',
            'members' => 'requiredIf:is_target,true|array',
            'members.*.user_id' => 'requiredIf:is_target,true|exists:users,id|distinct:strict',
            'members.*.targets' => 'requiredIf:is_target,true|array',
            'members.*.targets.*.year' => 'requiredIf:is_target,true|integer|min:2025',
            'members.*.targets.*.part' => 'requiredIf:is_target,true|integer|min:1|max:12',
            'members.*.targets.*.amount' => 'requiredIf:is_target,true|numeric|min:1',
        ];
    }

    public function validatedData(): array
    {
        $data = $this->validated();
        if (!$this->boolean('is_target')) {
            unset($data['members'], $data['period_type']);
        }
        return $data;
    }
}
