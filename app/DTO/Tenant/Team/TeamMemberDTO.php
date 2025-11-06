<?php

namespace App\DTO\Tenant\Team;

use App\DTO\BaseDTO;
use App\DTO\Interfaces\DTOInterface;
use Illuminate\Http\Request;

class TeamMemberDTO extends BaseDTO implements DTOInterface
{
    public function __construct(
        public int $user_id,
        public array $targets,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            user_id: $data['user_id'],
            targets: $data['targets'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'targets' => $this->targets,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user_id,
            targets: $request->targets,
        );
    }
}
