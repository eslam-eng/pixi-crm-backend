<?php
namespace App\DTO\Service;
use App\DTO\BaseDTO;
use Illuminate\Support\Arr;
class ServiceDTO extends BaseDTO
{
    public function __construct(
        protected string $name,
        protected ?float $price = null,
        protected array $categories = []
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->name,
            price: $request->price ? (float) $request->price : null,
            categories: $request->categories ?? [] 
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            price: Arr::get($data, 'price') ? (float) Arr::get($data, 'price') : null,
            categories: Arr::get($data, 'categories', [])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'categories' => $this->categories,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}
