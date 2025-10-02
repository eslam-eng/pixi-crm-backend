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

    public function punch(ClickRequest $clickRequest)
    {
        $data = $clickRequest->validated();

        $data = $this->attendanceService->punch(
            userId: auth()->id(),
            type: $data['type'] ?? 'in',
            meta: [
                'source' => 'web',
                'request_uuid' => Str::uuid()->toString(),
                'lat' => $data['lat'],
                'lng' => $data['lng']
            ]
        );

        return apiResponse(message: 'Punched ' . $data->type . ' successfully');
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
            userId: $req->user_id ?? auth()->id(),
        );
        return apiResponse(message: 'Clicks retrieved successfully', data: $clicks);
    }
}
