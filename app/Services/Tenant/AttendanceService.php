<?php

namespace App\Services\Tenant;

use App\Models\Tenant\Attendance\AttendanceDay;
use App\Models\Tenant\Attendance\AttendancePunch;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;

class AttendanceService
{
    public function punch(int $userId, string $type, array $meta = []): AttendancePunch
    {
        // $type in ['in','out']
        return DB::transaction(function () use ($userId, $type, $meta) {
            $nowUtc = now()->utc();

            // Soft guard: last punch type cannot equal new type (unless forced)
            $last = AttendancePunch::where('user_id', $userId)->latest('happened_at')->first();
            if ($last && $last->type === $type) {
                // either return last or throw validation exception
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'type' => ["Last punch is already '{$type}'. You must punch the opposite type."]
                ]);
            }

            $punch = AttendancePunch::create([
                'user_id' => $userId,
                'type'    => $type,
                'happened_at' => $nowUtc,
                'source'  => $meta['source'] ?? 'web',
                'ip'      => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_uuid' => $meta['request_uuid'] ?? null,
                'latitude' => $meta['lat'] ?? null,
                'longitude' => $meta['lng'] ?? null,
            ]);

            // Rebuild daily totals for the affected local dates (could be two if overnight)
            $this->rebuildDailyForUserAround($userId, CarbonImmutable::parse($nowUtc));

            return $punch;
        });
    }

    public function rebuildDailyForUserAround(int $userId, CarbonImmutable $aroundUtc, string $tz = 'Africa/Cairo'): void
    {
        // The punch can affect the day of 'aroundUtc' in local time, and possibly the adjacent day.
        $local = $aroundUtc->setTimezone($tz);
        $dates = collect([
            $local->toDateString(),
            $local->copy()->subDay()->toDateString(),
            $local->copy()->addDay()->toDateString(),
        ])->unique();

        foreach ($dates as $date) {
            $this->rebuildDaily($userId, $date, $tz);
        }
    }

    public function rebuildDaily(int $userId, string $workDate, string $tz = 'Africa/Cairo'): void
    {
        // Load all punches that could touch this local calendar day (±1 day UTC buffer)
        $startLocal = CarbonImmutable::parse($workDate, $tz)->startOfDay();
        $endLocal   = CarbonImmutable::parse($workDate, $tz)->endOfDay();

        // Convert to UTC range window with buffer
        $windowStartUtc = $startLocal->subDay()->utc();
        $windowEndUtc   = $endLocal->addDay()->utc();

        $punches = AttendancePunch::where('user_id', $userId)
            ->whereBetween('happened_at', [$windowStartUtc, $windowEndUtc])
            ->orderBy('happened_at')
            ->get();

        // Pair into intervals
        $intervals = [];
        $stackIn = null;
        foreach ($punches as $p) {
            if ($p->type === 'in') {
                $stackIn = $p;
            } elseif ($p->type === 'out' && $stackIn) {
                $in  = $stackIn->happened_at->clone()->setTimezone($tz);
                $out = $p->happened_at->clone()->setTimezone($tz);
                if ($out->lt($in)) { // guard corrupted order
                    [$in, $out] = [$out, $in];
                }
                // Split interval if it crosses the day boundary
                $intervals = array_merge($intervals, $this->splitIntervalByDay($in, $out, $tz));
                $stackIn = null;
            }
        }
        // If last IN has no OUT -> keep it open but count nothing for closed totals
        // (optional: auto-close at end of day or next morning with policy)

        // Filter only intervals that fall on $workDate
        $intervalsForDate = array_values(array_filter($intervals, fn($seg) => $seg['date'] === $workDate));

        // Sum minutes
        $totalMinutes = 0;
        $displayIntervals = [];
        foreach ($intervalsForDate as $seg) {
            $mins = $seg['start']->diffInMinutes($seg['end']);
            $totalMinutes += $mins;
            $displayIntervals[] = [
                'in'  => $seg['start']->format('H:i'),
                'out' => $seg['end']->format('H:i'),
            ];
        }

        AttendanceDay::updateOrCreate(
            ['user_id' => $userId, 'work_date' => $workDate],
            [
                'total_minutes' => $totalMinutes,
                'intervals'     => $displayIntervals,
                'status'        => 'open', // or auto 'closed' if you finalize nightly
            ]
        );
    }

    private function splitIntervalByDay(CarbonImmutable $start, CarbonImmutable $end, string $tz): array
    {
        // returns segments: [['date'=>YYYY-MM-DD,'start'=>Carbon,'end'=>Carbon], ...]
        $segments = [];
        $cursorStart = $start;
        while ($cursorStart->toDateString() !== $end->toDateString()) {
            $dayEnd = $cursorStart->endOfDay();
            $segments[] = ['date' => $cursorStart->toDateString(), 'start' => $cursorStart, 'end' => $dayEnd];
            $cursorStart = $dayEnd->addSecond(); // next day start
        }
        $segments[] = ['date' => $end->toDateString(), 'start' => $cursorStart, 'end' => $end];
        return $segments;
    }

    public function getPunches(int $userId)
    {
        return AttendancePunch::where('user_id', $userId)->orderBy('happened_at', 'desc')->get();
    }
}
