<?php

namespace App\Services\Central;

use App\Models\ActivationCode;
use App\Models\DiscountCode;
use App\QueryFilters\ActivationCodeFilters;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivationCodeService extends BaseService
{
    public function __construct(
        public ActivationCode $model
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
        $code = $data['code'] ?? null;
        $number_of_codes = $data['number_of_codes'] ?? null;
        $code_parts = $data['code_parts'] ?? null;
        $part_length = $data['part_length'] ?? null;

        if ($code) {
            $this->getModel()->create([
                'code' => $code,
                'tier_id' => $data['tier_id'],
                'create_by' => auth()->user()->id,
                'source' => 'manual',
                'status' => $data['status'] ?? 'active',
                'trial_days' => 30,
                'expires_at' => now()->addDays(30),
            ]);
            return "Code created successfully";
        } elseif ($number_of_codes && $code_parts && $part_length) {
            $codes = $this->generateCodes($number_of_codes, $code_parts, $part_length);
            $codes = array_unique($codes);
            $codes = array_filter($codes, function ($code) {
                return !$this->getModel()->where('code', $code)->exists();
            });
            if (empty($codes)) {
                return "All codes already exist";
            }


            $baseData = [
                'tier_id' => $data['tier_id'],
                'create_by' => auth()->user()->id,
                'source' => 'manual',
                'status' => $data['status'] ?? 'active',
                'trial_days' => 30,
                'expires_at' => now()->addDays(30),
            ];
            $insertData = [];
            foreach ($codes as $code) {
                $insertData[] = [
                    ...$baseData,
                    'code' => $code,
                ];
            }

            $this->getModel()->insert($insertData);
            return count($codes) . " Codes created successfully";
        }

        return true;
    }

    public function update(array $data)
    {
        $activationCode = $this->findById($data['id']);
        // if (!$activationCode) {
        //     return "Activation code not found";
        // }

        $activationCode->update([
            'code' => $data['code'],
            'tier_id' => $data['tier_id'],
            'status' => $data['status'] ?? 'active',
            'trial_days' => $data['trial_days'] ?? 30,
            'expires_at' => now()->addDays($data['trial_days'] ?? 30),
        ]);

        return "Activation code updated successfully";
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


    public function statistics()
    {
        $total_codes = $this->getModel()->count();
        $total_active_codes = $this->getModel()->where('status', 'active')->count();
        $total_used_codes = $this->getModel()->whereNotNull('used_at')->count();
        $total_unused_codes = $this->getModel()->whereNull('used_at')->count();
        $expired_codes = $this->getModel()->where('expires_at', '<', now())->count();
        return [
            'total_codes' => $total_codes,
            'total_used_codes' => $total_used_codes,
            'total_unused_codes' => $total_unused_codes,
            'expired_codes' => $expired_codes,
        ];
    }

    public function show($id, array $withRelations = [])
    {
        $activationCode = $this->findById($id, ['*'], $withRelations);
        return $activationCode;
    }

    public function destroy($id)
    {
        $activationCode = $this->findById($id);
        if (!$activationCode) {
            return "Activation code not found";
        }

        // Check if the activation code is used
        if ($activationCode->used_at) {
            return "Cannot delete a used activation code";
        }

        // Delete the activation code
        $activationCode->delete();
        return "Activation code deleted successfully";
    }
}
