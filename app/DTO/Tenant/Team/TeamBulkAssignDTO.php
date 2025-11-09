<?php

namespace App\DTO\Tenant\Team;

use App\DTO\BaseDTO;
use App\DTO\Interfaces\DTOInterface;
use Illuminate\Http\Request;

class TeamBulkAssignDTO extends BaseDTO implements DTOInterface
{
    public function __construct(
        public string $team_name,
        public ?string $description,
        public int $team_leader_id,
        public array $sales,
        public string $status,
        public bool $is_target,
        public ?string $period_type,
        public ?array $members,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            team_name: $data['team_name'],
            description: $data['description'] ?? null,
            team_leader_id: $data['team_leader_id'],
            sales: $data['sales'],
            status: $data['status'],
            is_target: $data['is_target'],
            period_type: $data['period_type'] ?? null,
            members: $data['members'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'team_name' => $this->team_name,
            'description' => $this->description,
            'team_leader_id' => $this->team_leader_id,
            'sales' => $this->sales,
            'status' => $this->status,
            'is_target' => $this->is_target,
            'period_type' => $this->period_type,
            'members' => $this->members,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            team_name: $request->team_name,
            description: $request->description,
            team_leader_id: $request->team_leader_id,
            sales: $request->sales,
            status: $request->status,
            is_target: $request->is_target,
            period_type: $request->period_type,
            members: $request->members,
        );
    }
}
