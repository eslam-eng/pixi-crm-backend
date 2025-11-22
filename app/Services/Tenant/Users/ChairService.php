<?php

namespace App\Services\Tenant\Users;

use App\Models\User;
use App\Models\Team;
use App\Models\Tenant\Chair;
use App\QueryFilters\Tenant\ChairFilters;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\BaseService;

class ChairService extends BaseService
{
    public function __construct(private Chair $model) {}

    public function getModel(): Chair
    {
        return $this->model;
    }
    
    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 10): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): \Illuminate\Database\Eloquent\Builder
    {
        $users = $this->getQuery()->with($withRelations);
        return $users->filter(new ChairFilters($filters));
    }

    public function assignTeamChair(User $user, Team $team, ?Carbon $startDate = null): Chair
    {
        $startDate = $startDate ?? Carbon::today();

        return $this->model->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'started_at' => $startDate,
            'ended_at' => null
        ]);
    }

    /**
     * Assign user as individual chair (no team)
     */
    public function assignIndividualChair(User $user, ?Carbon $startDate = null): Chair
    {
        $startDate = $startDate ?? Carbon::today();

        return Chair::create([
            'user_id' => $user->id,
            'team_id' => null, // No team
            'started_at' => $startDate,
            'ended_at' => null
        ]);
    }

    /**
     * End a chair assignment
     */
    public function endChairAssignment(Chair $chair, ?Carbon $endDate = null): bool
    {
        $endDate = $endDate ?? Carbon::today();

        return $chair->update(['ended_at' => $endDate]);
    }

    /**
     * Transfer chair from one user to another (for team chairs)
     */
    public function transferTeamChair(Chair $currentChair, User $newUser, ?Carbon $transferDate = null): Chair
    {
        if (!$currentChair->hasTeam()) {
            throw new \Exception('Cannot transfer individual chair. Use transferIndividualChair instead.');
        }

        $transferDate = $transferDate ?? Carbon::today();

        return DB::transaction(function () use ($currentChair, $newUser, $transferDate) {
            // End current chair assignment
            $currentChair->update(['ended_at' => $transferDate]);

            // Create new chair assignment
            return Chair::create([
                'user_id' => $newUser->id,
                'team_id' => $currentChair->team_id,
                'started_at' => $transferDate,
                'ended_at' => null
            ]);
        });
    }

    /**
     * Transfer individual chair from one user to another
     */
    public function transferIndividualChair(Chair $currentChair, User $newUser, ?Carbon $transferDate = null): Chair
    {
        if ($currentChair->hasTeam()) {
            throw new \Exception('Cannot transfer team chair. Use transferTeamChair instead.');
        }

        $transferDate = $transferDate ?? Carbon::today();

        return DB::transaction(function () use ($currentChair, $newUser, $transferDate) {
            // End current chair assignment
            $currentChair->update(['ended_at' => $transferDate]);

            // Create new chair assignment
            return Chair::create([
                'user_id' => $newUser->id,
                'team_id' => null,
                'started_at' => $transferDate,
                'ended_at' => null
            ]);
        });
    }

    /**
     * Get active chair for a team
     */
    public function getActiveTeamChair(Team $team): ?Chair
    {
        return $this->model->where('team_id', $team->id)
            ->whereNull('ended_at')
            ->with('user')
            ->first();
    }

    /**
     * Get active individual chair for a user
     */
    public function getActiveIndividualChair(User $user): ?Chair
    {
        return $this->model->where('user_id', $user->id)
            ->whereNull('team_id')
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Get all active team chairs
     */
    public function getAllActiveTeamChairs()
    {
        return $this->model->active()
            ->withTeam()
            ->with(['user', 'team'])
            ->get();
    }

    /**
     * Get all active individual chairs
     */
    public function getAllActiveIndividualChairs()
    {
        return $this->model->active()
            ->individual()
            ->with('user')
            ->get();
    }

    /**
     * Convert individual chair to team chair
     */
    public function convertToTeamChair(Chair $individualChair, Team $team, ?Carbon $conversionDate = null): Chair
    {
        if ($individualChair->hasTeam()) {
            throw new \Exception('Chair already belongs to a team.');
        }

        $conversionDate = $conversionDate ?? Carbon::today();

        return DB::transaction(function () use ($individualChair, $team, $conversionDate) {
            // End individual chair
            $individualChair->update(['ended_at' => $conversionDate]);

            // Create team chair
            return $this->model->create([
                'user_id' => $individualChair->user_id,
                'team_id' => $team->id,
                'started_at' => $conversionDate,
                'ended_at' => null
            ]);
        });
    }

    /**
     * Convert team chair to individual chair
     */
    public function convertToIndividualChair(Chair $teamChair, ?Carbon $conversionDate = null): Chair
    {
        if (!$teamChair->hasTeam()) {
            throw new \Exception('Chair is already individual.');
        }

        $conversionDate = $conversionDate ?? Carbon::today();

        return DB::transaction(function () use ($teamChair, $conversionDate) {
            // End team chair
            $teamChair->update(['ended_at' => $conversionDate]);

            // Create individual chair
            return $this->model->create([
                'user_id' => $teamChair->user_id,
                'team_id' => null,
                'started_at' => $conversionDate,
                'ended_at' => null
            ]);
        });
    }
}
