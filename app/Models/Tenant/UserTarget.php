<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserTarget extends Model
{
    protected $fillable = [
        'user_id',
        'target_value',
        'created_at'
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'created_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForMonth($query, $date)
    {
        $from = Carbon::parse($date)->startOfMonth();
        $to   = Carbon::parse($date)->endOfMonth();

        return $query->whereBetween('created_at', [$from, $to]);
    }
}
