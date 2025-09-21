<?php

namespace App\Traits;

use App\Enums\ActivationStatus;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasAddresses
{

    public function addresses(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class,'addressable');
    }

    public function storeAddress(array $data = [])
    {
        return $this->addresses()->create($data);
    }

    public function deleteAddresses(): int
    {
        return $this->addresses()->delete();
    }

    public function updateAddress(array $data = [])
    {
        return $this->addresses()->update($data);
    }


}
