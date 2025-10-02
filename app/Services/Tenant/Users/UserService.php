<?php

namespace App\Services\Tenant\Users;

use App\DTO\Tenant\UserDTO;
use App\Enums\TargetType;
use App\Models\Tenant\User;
use App\Notifications\Tenant\WelcomeNewUserNotification;
use App\QueryFilters\Tenant\UsersFilters;
use App\Services\BaseService;
use App\Settings\UsersSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class UserService extends BaseService
{
    public function __construct(private User $model) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function updateLastLoginAt(int $userId): bool
    {
        $user = $this->findById($userId);
        $user->update(['last_login_at' => now()]);
        return true;
    }

    public function toggleStatus(int $userId): bool
    {
        $user = $this->findById($userId);
        $user->update(['is_active' => !$user->is_active]);
        return true;
    }

    public function changeLanguage(int $userId, string $language): bool
    {
        $user = $this->findById($userId);
        $user->update(['lang' => $language]);
        return true;
    }

    public function getLanguage(int $userId): string
    {
        $user = $this->findById($userId);
        return $user->lang;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 10): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): builder
    {
        $users = $this->getQuery()->with($withRelations);
        return $users->filter(new UsersFilters($filters));
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
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations)->orderBy('id', 'desc');
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(UserDTO $userDTO)
    {
        $data = $userDTO->toArray();
        $user = $this->getModel()->create($data);
        // Get role by ID and assign by name
        if ($userDTO->role) {
            $user->assignRole($userDTO->role);
        }
        $settings = app(UsersSettings::class);
        if ($settings->default_send_email_notifications) {
            $user->notify(new WelcomeNewUserNotification());
        }

        return $user->load('roles');
    }

    public function update(UserDTO $userDTO, $id)
    {
        $user = $this->findById($id);
        $data = $userDTO->toArray();

        if (!isset($data['password'])) {
            $user->update(Arr::except($data, ['password']));
        } else {
            $user->update($data);
        }

        // Handle role assignment
        if ($userDTO->role) {
            $user->syncRoles([$userDTO->role]);
        }

        return $user;
    }

    public function updateProfile($id, array $data = [])
    {
        $user = $this->findById($id);
        if (!isset($data['password']))
            $user->update(Arr::except($data, ['password']));
        else {
            $data['password'] = bcrypt($data['password']);
            $user->update($data);
        }
        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->findById($id);
        // $user->deleteAttachments();
        $user->roles()->detach();

        $user->delete();
        return true;
    }

    public function getTarget($user_id)
    {
        $user = $this->findById($user_id);

        if (!$user->target || $user->target_type === TargetType::NONE) {
            return [
                'target' => 0,
                'target_type' => $user->target_type,
                'achieved_amount' => 0,
                'achievement_percentage' => 0,
                'is_target_achieved' => false,
                'deals_count' => 0,
                'period' => null,
            ];
        }

        $period = $this->getTargetPeriod($user->target_type);
        $deals = $this->getUserDealsForPeriod($user_id, $period);

        $achieved_amount = $deals->sum('total_amount');
        $achievement_percentage = $user->target > 0 ? ($achieved_amount / $user->target) * 100 : 0;
        $is_target_achieved = $achieved_amount >= $user->target;

        return [
            'target' => $user->target,
            'target_type' => $user->target_type,
            'achieved_amount' => $achieved_amount,
            'achievement_percentage' => round($achievement_percentage, 2),
            'is_target_achieved' => $is_target_achieved,
            'deals_count' => $deals->count(),
            'period' => $period,
            'deals' => $deals->map(function ($deal) {
                return [
                    'id' => $deal->id,
                    'deal_name' => $deal->deal_name,
                    'total_amount' => $deal->total_amount,
                    'sale_date' => $deal->sale_date,
                    'payment_status' => $deal->payment_status,
                ];
            }),
        ];
    }

    /**
     * Get target period based on target type
     */
    private function getTargetPeriod($targetType)
    {
        return match ($targetType) {
            TargetType::MONTHLY->value => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'label' => now()->format('F Y'),
            ],
            TargetType::CALENDAR_QUARTERS->value => [
                'start' => now()->startOfQuarter(),
                'end' => now()->endOfQuarter(),
                'label' => 'Q' . now()->quarter . ' ' . now()->year,
            ],
            default => null,
        };
    }

    /**
     * Get user deals for specific period
     */
    private function getUserDealsForPeriod($user_id, $period)
    {
        if (!$period) {
            return collect();
        }

        return $this->model->find($user_id)->deals()
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->where('approval_status', 'approved') // Only count approved deals
            ->get();
    }
}
