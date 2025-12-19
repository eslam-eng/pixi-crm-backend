<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class TenantUser extends Model
{
    protected $table = 'tenant_users';

    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'email',
        'name',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
