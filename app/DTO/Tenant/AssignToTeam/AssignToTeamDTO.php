<?php

namespace App\DTO\Tenant\AssignToTeam;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class AssignToTeamDTO extends BaseDTO
{
    /**
     * @param ?int $team_id',
     * @param ?int $user_id',
     * @param ?array $monthly_target',
     * @param ?array $quarterly_target',
     */

    public function __construct(
        public readonly ?int $team_id,
        public readonly ?int $user_id,
        public readonly ?array $monthly_target,
        public readonly ?array $quarterly_target,
    ) {}

    public static function fromRequest($request): BaseDTO
    {
        return new self(
            team_id: $request->team_id,
            user_id: $request->user_id,
            monthly_target: $request->monthly_target,
            quarterly_target: $request->quarterly_target,
        );
    }

    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            team_id: Arr::get($data, 'team_id'),
            user_id: Arr::get($data, 'user_id'),
            monthly_target: Arr::get($data, 'monthly_target'),
            quarterly_target: Arr::get($data, 'quarterly_target'),

        );
    }

    public function toArray(): array
    {
        return [
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'monthly_target' => $this->monthly_target,
            'quarterly_target' => $this->quarterly_target,
        ];
    }
}
