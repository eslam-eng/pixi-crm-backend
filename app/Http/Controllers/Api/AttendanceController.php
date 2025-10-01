<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Users\ClickRequest;
use App\Services\Tenant\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tenant\Attendance\AttendanceDay;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}
    public function punchIn(ClickRequest $req)
    {
        dd($req->all());
        $this->attendanceService->punch(
            userId: auth()->id(),
            type: 'in',
            meta: ['source' => 'web', 'request_uuid' => Str::uuid()->toString()]
        );
        return apiResponse(message: 'Punched in');
    }

    public function punchOut()
    {
        $this->attendanceService->punch(
            userId: auth()->id(),
            type: 'out',
            meta: ['source' => 'web', 'request_uuid' => Str::uuid()->toString()]
        );
        return apiResponse(message: 'Punched out');
    }

    public function index(Request $req)
    {
        $q = AttendanceDay::query()
            ->with('user:id,first_name,last_name')
            ->when($req->user_id, fn($x) => $x->where('user_id', $req->user_id))
            ->when($req->from, fn($x) => $x->whereDate('work_date', '>=', $req->from))
            ->when($req->to, fn($x) => $x->whereDate('work_date', '<=', $req->to))
            ->orderByDesc('work_date');

        return response()->json($q->paginate(30));
    }

    public function clicks(Request $req)
    {
        $clicks = $this->attendanceService->getPunches(
            userId: auth()->id(),
        );
        return apiResponse(message: 'Clicks retrieved successfully', data: $clicks);
    }
}
