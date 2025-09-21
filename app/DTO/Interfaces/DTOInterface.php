<?php

namespace App\DTO\Interfaces;

use Illuminate\Http\Request;

interface DTOInterface
{
    public static function fromRequest(Request $request): self;

    public static function fromArray(array $data): self;

    public function toArray(): array;

}
