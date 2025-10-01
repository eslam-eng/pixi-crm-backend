<?php

namespace App\Services;

use App\DTO\Team\TeamDTO;
use App\Exceptions\GeneralException;
use App\Models\Team;
use App\QueryFilters\Tenant\TeamFilters;
use App\Services\BaseService;
use App\Services\Tenant\Users\UserService;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
}
