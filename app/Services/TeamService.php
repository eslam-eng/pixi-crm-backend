<?php

namespace App\Services;

use App\DTO\Team\TeamDTO;
use App\DTO\Tenant\Team\TargetMemberDTO;
use App\DTO\Tenant\Team\TeamBulkAssignDTO;
use App\DTO\Tenant\Team\TeamMemberDTO;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Team;
use App\QueryFilters\Tenant\TeamFilters;
use App\Services\BaseService;
use App\Services\Tenant\Users\UserService;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class TeamService extends BaseService
{
    public function __construct(
        private Team $model,
        private UserService $userService
    ) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 10): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): builder
    {
        $teams = $this->getQuery()->with($withRelations);
        return $teams->filter(new TeamFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = []): Builder
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations);
    }

    public function getUsersForSelectDropDown(array $filters = []): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->queryGet(filters: $filters)->select(['id', 'name'])->get();
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(TeamDTO $teamDTO)
    {
        $team = $this->model->create($teamDTO->toArray());
        if (filled($teamDTO->sales_ids))
            $this->syncTeamMembers($team, $teamDTO->sales_ids, $teamDTO->leader_id);
        return $team->load('leader.roles', 'sales.roles');
    }

    public function update(TeamDTO $teamDTO, int $id)
    {
        $team = $this->findById($id);
        $team->update($teamDTO->toArray());
        if (filled($teamDTO->sales_ids))
            $this->syncTeamMembers($team, $teamDTO->sales_ids, $teamDTO->leader_id);
        return true;
    }

    public function destroy($id)
    {
        $team = $this->findById($id);

        if (filled($team->sales))
            throw new GeneralException(trans('app.team_has_sales'));
        return $team->delete();
    }

    private function syncTeamMembers(Team $team, array $incomingIds, ?int $leaderId = null): void
    {
        DB::transaction(function () use ($team, $incomingIds, $leaderId) {
            // 1) Normalize input
            $incomingIds = collect($incomingIds)
                ->filter()                 // remove null/empty
                ->map(fn($v) => (int)$v)
                ->unique()
                ->values()
                ->all();

            // Ensure leader is included (optional but recommended)
            if ($leaderId) {
                $incomingIds = collect($incomingIds)->push((int)$leaderId)->unique()->values()->all();
            }

            // 2) Current members for this team
            $currentIds = $this->userService->getModel()->where('team_id', $team->id)->pluck('id')->all();

            // 3) Diff
            $toAttach = array_values(array_diff($incomingIds, $currentIds)); // newly added
            $toDetach = array_values(array_diff($currentIds, $incomingIds)); // removed

            // 4) Apply (scoped, safe)
            if (!empty($toAttach)) {
                $this->userService
                    ->getModel()
                    ->whereIn('id', $toAttach)
                    ->update(['team_id' => $team->id]);
            }
            if (!empty($toDetach)) {
                $this->userService->getModel()
                    ->whereIn('id', $toDetach)
                    ->update(['team_id' => null]);
            }

            // 5) Hard guarantee for leader (in case of races)
            if ($leaderId) {
                $this->userService
                    ->getModel()
                    ->whereKey($leaderId)
                    ->update(['team_id' => $team->id]);
            }

            // Optional: touch the team to update timestamps
            $team->touch();
        });
    }

    public function teamBulkAssign(TeamBulkAssignDTO $teamBulkAssignDTO)
    {
        $leader = $this->userService->getModel()
            ->role(['admin', 'manager'])
            ->find($teamBulkAssignDTO->team_leader_id);

        if (!$leader) {
            throw new GeneralException('leader is not admin or manager');
        }

        $team = $this->model->create([
            'title' => $teamBulkAssignDTO->team_name,
            'description' => $teamBulkAssignDTO->description,
            'leader_id' => $teamBulkAssignDTO->team_leader_id,
            'status' => $teamBulkAssignDTO->status,
            'is_target' => $teamBulkAssignDTO->is_target,
            'period_type' => $teamBulkAssignDTO->period_type,
        ]);

        $allSales = array_merge($teamBulkAssignDTO->sales, collect($teamBulkAssignDTO->members)->pluck('user_id')->toArray());
        $allSales = array_unique($allSales);

        $someUsersExistInOtherTeam = $this->userService->getModel()
            ->whereIn('id', $allSales)
            ->whereNotNull('team_id')
            ->exists();

        if ($someUsersExistInOtherTeam) {
            throw new GeneralException('Some users are already assigned to other team');
        } else {
            $this->userService->getModel()->whereIn('id', $allSales)->update(['team_id' => $team->id]);
        }

        if (! $teamBulkAssignDTO->is_target) {
            return $team->load('chairs.targets', 'leader.roles', 'chairs.user', 'members');
        }

        switch ($teamBulkAssignDTO->period_type) {
            case 'monthly':
                $this->createMonthlyTargetsForTeamMembers($team, $teamBulkAssignDTO->members);
                break;

            case 'quarterly':
                $this->createQuarterlyTargetsForTeamMembers($team, $teamBulkAssignDTO->members);
                break;
        }
        return $team->load('chairs.targets', 'leader.roles', 'chairs.user', 'members');
    }

    private function createMonthlyTargetsForTeamMembers(Team $team, array $members)
    {
        $validator = validator([], []);
        foreach ($members as $member) {
            $memberDTO = TeamMemberDTO::fromArray($member);
            $user = $this->userService->findById($memberDTO->user_id);
            $chair = $user->chairs()->create([
                'team_id' => $team->id,
                'started_at' => now(),
                'ended_at' => null,
            ]);

            $this->checkIfPartsValuesAreUnique($memberDTO->targets, 'monthly');

            foreach ($memberDTO->targets as $index => $target) {
                $target = TargetMemberDTO::fromArray($target);
                if ($this->IsAllowMonthlyTarget($target->part, $target->year)) {
                    $chair->targets()->create([
                        'period_type' => "monthly",
                        'year' => $target->year,
                        'period_number' => $target->part,
                        'effective_from' => now()->copy()->year((int)$target->year)->month((int)$target->part)->startOfMonth()->format('Y-m-d H:i:s'),
                        'effective_to' => now()->copy()->year((int)$target->year)->month((int)$target->part)->endOfMonth()->format('Y-m-d H:i:s'),
                        'target_value' => $target->amount,
                    ]);
                } else {
                    $monthName = now()->copy()->year((int)$target->year)->month((int)$target->part);
                    $validator->errors()->add($index . ".month", "You are not allowed to set target for before month " . $monthName->format('F Y'));
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
    private function createQuarterlyTargetsForTeamMembers(Team $team, array $members)
    {
        $validator = validator([], []);
        foreach ($members as $member) {
            $memberDTO = TeamMemberDTO::fromArray($member);
            $user = $this->userService->findById($memberDTO->user_id);
            $chair = $user->chairs()->create([
                'team_id' => $team->id,
                'started_at' => now(),
                'ended_at' => null,
            ]);

            $this->checkIfPartsValuesAreUnique($memberDTO->targets, 'quarterly');

            foreach ($memberDTO->targets as $index => $target) {
                $target = TargetMemberDTO::fromArray($target);
                if ($this->IsAllowQuarterlyTarget($target->part, $target->year, $validator)) {

                    [$startOfQuarter, $endOfQuarter] = $this->getStartAndEndOfQuarter($target->year, $target->part);
                    $chair->targets()->create([
                        'period_type' => "quarterly",
                        'year' => $target->year,
                        'period_number' => $target->part,
                        'effective_from' => $startOfQuarter->format('Y-m-d H:i:s'),
                        'effective_to' => $endOfQuarter->format('Y-m-d H:i:s'),
                        'target_value' => $target->amount,
                    ]);
                } else {
                    $quarterName = now()->copy()->year((int)$target->year)->setQuarter((int)$target->part);
                    $validator->errors()->add($index . ".quarter", "You are not allowed to set target for before quarter " . $quarterName->format('F Y'));
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }

    public function IsAllowMonthlyTarget($month, $year): bool
    {
        $selectedDate  = now()->copy()->year((int)$year)->month((int)$month)->startOfMonth();
        $minDateAllowed = now()->copy()->startOfMonth();
        return ($selectedDate >= $minDateAllowed);
    }

    public function IsAllowQuarterlyTarget($quarter, $year, $validator): bool
    {
        if ($quarter < 1 || $quarter > 4) {
            $validator->errors()->add("quarter", "Quarter must be between 1 and 4");
        }

        $selectedDate  = now()->copy()->year((int)$year)->setQuarter((int)$quarter)->startOfQuarter();
        $minDateAllowed = now()->copy()->startOfQuarter();
        return ($selectedDate >= $minDateAllowed);
    }

    public function getStartAndEndOfQuarter($year, $quarter): array
    {
        $month = ($quarter - 1) * 3 + 1;
        return [
            now()->copy()->year($year)->month($month)->startOfMonth(),
            now()->copy()->year($year)->month($month)->addMonths(2)->endOfMonth(),
        ];
    }

    public function checkIfPartsValuesAreUnique($targets, $type = 'quarterly'): void
    {
        $parts = collect($targets)->pluck('part');
        if ($parts->duplicates()->isNotEmpty()) {
            throw new GeneralException($parts->duplicates()->implode(', ') . " is duplicate " . $type . " values found");
        }
    }

    public function findByIdWithTarget(int $id): Team
    {
        return $this->model->with(['chairs.targets', 'chairs.user', 'leader.roles', 'members'])->findOrFail($id);
    }

    public function teamBulkUpdate(TeamBulkAssignDTO $teamBulkAssignDTO , int $id)
    {
        $leader = $this->userService->getModel()
            ->role(['admin', 'manager'])
            ->find($teamBulkAssignDTO->team_leader_id);

        if (!$leader) {
            throw new GeneralException('leader is not admin or manager');
        }

        $team = $this->findById($id);

        $team->update([
            'title' => $teamBulkAssignDTO->team_name,
            'description' => $teamBulkAssignDTO->description,
            'leader_id' => $teamBulkAssignDTO->team_leader_id,
            'status' => $teamBulkAssignDTO->status,
            'is_target' => $teamBulkAssignDTO->is_target,
            'period_type' => $teamBulkAssignDTO->period_type,
        ]);

        $team->members()->update(['team_id' => null]);
        $allSales = array_merge($teamBulkAssignDTO->sales, collect($teamBulkAssignDTO->members)->pluck('user_id')->toArray());
        $allSales = array_unique($allSales);


        $someUsersExistInOtherTeam = $this->userService->getModel()
            ->whereIn('id', $allSales)
            ->whereNotNull('team_id')
            ->exists();

        if ($someUsersExistInOtherTeam) {
            throw new GeneralException('Some users are already assigned to other team');
        } else {
            $this->userService->getModel()->whereIn('id', $allSales)->update(['team_id' => $team->id]);
        }

        if (! $teamBulkAssignDTO->is_target) {
            return $team->load('chairs.targets', 'leader.roles', 'chairs.user', 'members');
        }

        switch ($teamBulkAssignDTO->period_type) {
            case 'monthly':
                $this->updateMonthlyTargetsForTeamMembers($team, $teamBulkAssignDTO->members);
                break;

            case 'quarterly':
                $this->updateQuarterlyTargetsForTeamMembers($team, $teamBulkAssignDTO->members);
                break;
        }
        return $team->load('chairs.targets', 'leader.roles', 'chairs.user', 'members');
    }

    private function updateMonthlyTargetsForTeamMembers(Team $team, array $members)
    {
        $validator = validator([], []);


        foreach ($members as $member) {
            $memberDTO = TeamMemberDTO::fromArray($member);
            $user = $this->userService->findById($memberDTO->user_id);

            $chair = $user->chairs()->where('team_id', $team->id)->first();
            if ($chair) {
                // Delete existing targets
                $chair->targets()->delete();
                // Delete chair record
                $chair->delete();
            }

            $chair = $user->chairs()->create([
                'team_id' => $team->id,
                'started_at' => now(),
                'ended_at' => null,
            ]);

            $this->checkIfPartsValuesAreUnique($memberDTO->targets, 'monthly');

            foreach ($memberDTO->targets as $index => $target) {
                $target = TargetMemberDTO::fromArray($target);
                if ($this->IsAllowMonthlyTarget($target->part, $target->year)) {
                    $chair->targets()->create([
                        'period_type' => "monthly",
                        'year' => $target->year,
                        'period_number' => $target->part,
                        'effective_from' => now()->copy()->year((int)$target->year)->month((int)$target->part)->startOfMonth()->format('Y-m-d H:i:s'),
                        'effective_to' => now()->copy()->year((int)$target->year)->month((int)$target->part)->endOfMonth()->format('Y-m-d H:i:s'),
                        'target_value' => $target->amount,
                    ]);
                } else {
                    $monthName = now()->copy()->year((int)$target->year)->month((int)$target->part);
                    $validator->errors()->add($index . ".month", "You are not allowed to set target for before month " . $monthName->format('F Y'));
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }

    private function updateQuarterlyTargetsForTeamMembers(Team $team, array $members)
    {
        $validator = validator([], []);
        foreach ($members as $member) {
            $memberDTO = TeamMemberDTO::fromArray($member);
            $user = $this->userService->findById($memberDTO->user_id);
            
            $chair = $user->chairs()->where('team_id', $team->id)->first();
            if ($chair) {
                // Delete existing targets
                $chair->targets()->delete();
                // Delete chair record
                $chair->delete();
            }
            
            $chair = $user->chairs()->create([
                'team_id' => $team->id,
                'started_at' => now(),
                'ended_at' => null,
            ]);

            $this->checkIfPartsValuesAreUnique($memberDTO->targets, 'quarterly');

            foreach ($memberDTO->targets as $index => $target) {
                $target = TargetMemberDTO::fromArray($target);
                if ($this->IsAllowQuarterlyTarget($target->part, $target->year, $validator)) {

                    [$startOfQuarter, $endOfQuarter] = $this->getStartAndEndOfQuarter($target->year, $target->part);
                    $chair->targets()->create([
                        'period_type' => "quarterly",
                        'year' => $target->year,
                        'period_number' => $target->part,
                        'effective_from' => $startOfQuarter->format('Y-m-d H:i:s'),
                        'effective_to' => $endOfQuarter->format('Y-m-d H:i:s'),
                        'target_value' => $target->amount,
                    ]);
                } else {
                    $quarterName = now()->copy()->year((int)$target->year)->setQuarter((int)$target->part);
                    $validator->errors()->add($index . ".quarter", "You are not allowed to set target for before quarter " . $quarterName->format('F Y'));
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }
    }
    
}
