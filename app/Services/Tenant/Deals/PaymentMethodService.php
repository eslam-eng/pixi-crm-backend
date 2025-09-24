<?php

namespace App\Services\Tenant\Deals;

use App\Exceptions\GeneralException;
use App\Models\Tenant\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\PaymentMethod\PaymentMethodDTO;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class PaymentMethodService extends BaseService
{
    public function __construct(
        public PaymentMethod $model,
    ) {}

    public function getModel(): PaymentMethod
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $defaultRelations = ['color'];
        $withRelations = array_merge($defaultRelations, $withRelations);
        $priorities = $this->model->with($withRelations)->ordered();
        return $priorities;
    }

    public function store(PaymentMethodDTO $PaymentMethodDTO): PaymentMethod
    {
        try {
            DB::beginTransaction();
            
            // If this PaymentMethod is set as default, unset all other defaults
            if ($PaymentMethodDTO->is_default) {
                $this->model->where('is_default', true)->update(['is_default' => false]);
            }
            
            $PaymentMethod = $this->model->create($PaymentMethodDTO->toArray());
            
            DB::commit();
            return $PaymentMethod;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to create PaymentMethod: ' . $e->getMessage());
        }
    }

    public function update(int $id, PaymentMethodDTO $PaymentMethodDTO): PaymentMethod
    {
        try {
            DB::beginTransaction();
            
            $PaymentMethod = $this->findById($id);
            
            // If this PaymentMethod is set as default, unset all other defaults
            if ($PaymentMethodDTO->is_default) {
                $this->model->where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
            }
            
            $PaymentMethod->update($PaymentMethodDTO->toArray());
            
            DB::commit();
            return $PaymentMethod->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update PaymentMethod: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $PaymentMethod = $this->findById($id);
            
            // Check if the payment method can be deleted using model method
            if (!$PaymentMethod->canDelete()) {
                throw new GeneralException('This payment method cannot be deleted because it is either a system payment method or has associated deals.');
            }
            
            $result = $PaymentMethod->delete();
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete PaymentMethod: ' . $e->getMessage());
        }
    }

    public function setDefault(int $id): PaymentMethod
    {
        try {
            DB::beginTransaction();
            
            $paymentMethod = $this->findById($id);
            
            // Unset all other defaults
            $this->model->where('is_default', true)->update(['is_default' => false]);
            
            // Set this PaymentMethod as default
            $paymentMethod->update(['is_default' => true]);
            
            DB::commit();
            return $paymentMethod->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to set default PaymentMethod: ' . $e->getMessage());
        }
    }

    public function setChecked(int $id): PaymentMethod
    {
        try {
            DB::beginTransaction();
            
            $paymentMethod = $this->findById($id);
     
            if($paymentMethod->is_checked == 1){
                $paymentMethod->is_checked = 0;
               
            }else{
                $paymentMethod->is_checked = 1;
            }
            $paymentMethod->save();
            
            DB::commit();
            return $paymentMethod->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to set default PaymentMethod: ' . $e->getMessage());
        }
    }

    public function getDefault(): ?PaymentMethod
    {
        return $this->model->default()->first();
    }
}