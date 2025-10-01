<?php

namespace App\Models;

use App\Models\Tenant\User;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory, Filterable;
    protected $table = 'teams';

    protected $fillable = [
        'title',
        'leader_id',
    ];

    public function leader()
    {
        return $this->hasOne(User::class, 'id', 'leader_id');
    }

    public function sales()
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }
}
