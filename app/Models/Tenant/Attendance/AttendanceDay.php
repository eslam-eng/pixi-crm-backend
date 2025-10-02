<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Model;

class AttendanceDay extends Model
{
    protected $fillable = ['user_id', 'work_date', 'total_minutes', 'paid_break_minutes', 'unpaid_break_minutes', 'intervals', 'status', 'latitude', 'longitude'];

    protected $casts = [
        'work_date' => 'date',
        'intervals' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
