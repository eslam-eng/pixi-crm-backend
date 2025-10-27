<?php

namespace App\Models\Central;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Admin;

class Role extends \Spatie\Permission\Models\Role
{
    use Filterable, HasFactory;

    protected $fillable = ['name', 'guard_name', 'is_active', 'description'];

    protected $table = 'roles';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', Admin::class);
    }
}
