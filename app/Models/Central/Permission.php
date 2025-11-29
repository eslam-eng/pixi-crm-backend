<?php

namespace App\Models\Central;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends \Spatie\Permission\Models\Permission
{
    use Filterable, HasFactory;
}
