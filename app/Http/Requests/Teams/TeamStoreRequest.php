<?php

namespace App\Http\Requests\Teams;

use App\DTO\Tenant\UserDTO;
use App\Http\Requests\BaseRequest;
use App\Rules\UserIsLeader;
use App\Rules\UserIsSales;

class TeamStoreRequest extends BaseRequest
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
        return [
            'title' => 'required|string|unique:teams,title|max:255',
            'leader_id' => ['required','integer','exists:users,id',new UserIsLeader()],
            'source_id' => 'required|integer|exists:sources,id',
            'location_id' => 'required|integer|exists:locations,id',
            'sales_ids' => 'nullable|array',
            'sales_ids.*' => ['required','integer','exists:users,id',new UserIsSales()],
        ];
    }

    public function toUserDTO(): \App\DTO\BaseDTO
    {
        return UserDTO::fromRequest($this);
    }
}
