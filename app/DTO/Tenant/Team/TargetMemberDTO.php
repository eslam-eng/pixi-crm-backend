<?php

namespace App\DTO\Tenant\Team;

use App\DTO\BaseDTO;
use App\DTO\Interfaces\DTOInterface;
use Illuminate\Http\Request;

class TargetMemberDTO extends BaseDTO implements DTOInterface
{
    public function __construct(

        public int $year,
        public int $part,
        public float $amount,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            year: $data['year'],
            part: $data['part'],
            amount: $data['amount'],
        );
    }

    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'part' => $this->part,
            'amount' => $this->amount,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            year: $request->year,
            part: $request->part,
            amount: $request->amount,
        );
    }
}
