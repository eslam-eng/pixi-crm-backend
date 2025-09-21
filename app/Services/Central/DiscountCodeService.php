<?php

namespace App\Services\Central;

use App\Models\DiscountCode;
use App\QueryFilters\ActivationCodeFilters;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeService extends BaseService
{
    public function __construct(
        public DiscountCode $model
    ) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $activationCodes = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $activationCodes->filter(new ActivationCodeFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        // dd($filters);
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }


    public function store(array $data)
    {
        $this->getModel()->create([
            'code' => $data['code'],
            'tier_id' => $data['tier_id'],
            'create_by' => auth()->user()->id,
            'source' => 'manual',
            'status' => $data['status'] ?? 'active',
            'discount_percentage' => $data['discount_percentage'],
            'usage_type' => $data['usage_type'],
            'max_uses' => $data['max_uses'],
            'trial_days' => 30,
            'expires_at' => now()->addDays(30),
        ]);
        return true;
    }

    public function generateCodes($number_of_codes, $code_parts, $part_length)
    {
        $codes = [];
        for ($i = 0; $i < $number_of_codes; $i++) {
            $codes[] = $this->generateCode($code_parts, $part_length);
        }
        return $codes;
    }

    public function generateCode($code_parts, $part_length)
    {
        $parts = [];
        for ($i = 0; $i < $code_parts; $i++) {
            $parts[] = str_pad(rand(0, pow(10, $part_length) - 1), $part_length, '0', STR_PAD_LEFT);
        }
        return implode('-', $parts);
    }
}
