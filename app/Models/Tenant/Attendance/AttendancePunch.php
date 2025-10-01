<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Model;

class AttendancePunch extends Model
{
    protected $fillable = ['user_id', 'type', 'happened_at', 'source', 'ip', 'user_agent', 'request_uuid'];

    protected $casts = [
        'happened_at' => 'immutable_datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
