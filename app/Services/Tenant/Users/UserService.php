<?php

namespace App\Services\Tenant\Users;

use App\DTO\Tenant\AssignToTeam\AssignToTeamDTO;
use App\DTO\Tenant\UserDTO;
use App\Enums\TargetType;
use App\Models\Tenant\Team;
use App\Models\Tenant\User;
use App\Notifications\Tenant\WelcomeNewUserNotification;
use App\QueryFilters\Tenant\UsersFilters;
use App\Services\BaseService;
use App\Settings\UsersSettings;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

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

        $validator = validator([], []); // Create empty validator
        if ($userDTO->targets) {
            $chair = $user->chairs()->create([
                'team_id' => null,
                'started_at' => now(),
                'ended_at' => null,
            ]);
        }

        if ($userDTO->period_type == "monthly") {
            foreach ($userDTO->targets as $index => $target) {
                if ($this->IsAllowMonthlyTarget($target['part'], $target['year'])) {
                    $chair->targets()->create([
                        'period_type' => $userDTO->period_type,
                        'year' => $target['year'],
                        'period_number' => $target['part'],
                        'target_value' => $target['amount'],
                        'effective_from' => now()->copy()->year((int)$target['year'])->month((int)$target['part'])->startOfMonth()->format('Y-m-d H:i:s'),
                        'effective_to' => now()->copy()->year((int)$target['year'])->month((int)$target['part'])->endOfMonth()->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    $monthName = now()->copy()->setMonth((int)$target['part']);
                    $validator->errors()->add($index . ".month", "You are not allowed to set target for before month " . $monthName->format('F Y'));
                }
            }
        }

        if ($userDTO->period_type == "quarterly") {
            foreach ($userDTO->targets as $index => $target) {
                if ($this->IsAllowQuarterlyTarget($target['part'], $target['year'])) {    
                    [$startOfQuarter, $endOfQuarter] = $this->getStartAndEndOfQuarter($target['year'], $target['part']);
                    $chair->targets()->create([
                        'period_type' => $userDTO->period_type,
                        'year' => $target['year'],
                        'period_number' => $target['part'],
                        'effective_from' => $startOfQuarter->format('Y-m-d H:i:s'),
                        'effective_to' => $endOfQuarter->format('Y-m-d H:i:s'),
                        'target_value' => $target['amount'],
                    ]);
                } else {
                    $validator->errors()->add($index . ".quarter", "You are not allowed to set target for before quarter " . $target['part']);
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
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

        if ($userDTO->targets) {
            if ($user->activeChairs()->exists()) {
                $chair = $user->activeChairs()->first();
                $chair->targets()->delete();
            } else {
                $chair = $user->chairs()->create([
                    'team_id' => null,
                    'started_at' => now(),
                    'ended_at' => null,
                ]);
            }
        } else {
            $user->activeChairs()->first()->update([
                'ended_at' => now(),
            ]);
        }

        $validator = validator([], []); // Create empty validator
        if ($userDTO->period_type == "monthly") {
            foreach ($userDTO->targets as $index => $target) {
                // if ($this->IsAllowMonthlyTarget($target['part'], $target['year'])) {
                    $chair->targets()->updateOrCreate([
                        'period_type' => $userDTO->period_type,
                        'year' => $target['year'],
                        'period_number' => $target['part'],
                        'effective_from' => now()->copy()->year((int)$target['year'])->month((int)$target['part'])->startOfMonth()->format('Y-m-d H:i:s'),
                        'effective_to' => now()->copy()->year((int)$target['year'])->month((int)$target['part'])->endOfMonth()->format('Y-m-d H:i:s'),
                    ], [
                        'target_value' => $target['amount'],
                    ]);
                // } else {
                //     $monthName = now()->copy()->setMonth((int)$target['part']);
                //     $validator->errors()->add($index . ".month", "You are not allowed to set target for before month " . $monthName->format('F Y'));
                // }
            }
        }

        if ($userDTO->period_type == "quarterly") {
            foreach ($userDTO->targets as $index => $target) {
                // if ($this->IsAllowQuarterlyTarget($target['part'], $target['year'])) {
                    [$startOfQuarter, $endOfQuarter] = $this->getStartAndEndOfQuarter($target['year'], $target['part']);
                    $chair->targets()->updateOrCreate([
                        'period_type' => "quarterly",
                        'year' => $target['year'],
                        'period_number' => $target['part'],
                        'effective_from' => $startOfQuarter->format('Y-m-d H:i:s'),
                        'effective_to' => $endOfQuarter->format('Y-m-d H:i:s'),
                    ], [
                        'target_value' => $target['amount'],
                    ]);
                // } else {
                //     $validator->errors()->add($index . ".quarter", "You are not allowed to set target for before quarter " . $target['quarter']);
                // }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
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

    public function destroy($id)
    {
        $user = $this->findById($id);
        $user->roles()->detach();
        $user->delete();
        return true;
    }

    public function getTargets($user_id)
    {
        $user = $this->findById($user_id);

        if (!$user->activeChair()->exists()) {
            throw ValidationException::withMessages([
                'user' => 'User does not have a target',
            ], 400);
        }
        $chair = $user->activeChair()->first();
        return $chair->targets()->get();
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

    public function assignToTeam(AssignToTeamDTO $assignToTeamDTO): User
    {
        $user = $this->findById($assignToTeamDTO->user_id);
        $team = Team::findOrFail($assignToTeamDTO->team_id);

        if ($user->team_id) {
            throw ValidationException::withMessages([
                'team' => 'User already has a team',
            ]);
        }

        if ($team->is_target) {
            if ($assignToTeamDTO->monthly_target || $assignToTeamDTO->quarterly_target) {
                $chair = $user->chairs()->create([
                    'team_id' => $assignToTeamDTO->team_id,
                    'started_at' => now(),
                    'ended_at' => null,
                ]);
            } else {
                throw ValidationException::withMessages([
                    'team' => 'Team is a target team, so you need to assign a target to him',
                ]);
            }
        }

        $user->update(['team_id' => $assignToTeamDTO->team_id]);


        $validator = validator([], []);
        if ($assignToTeamDTO->monthly_target) {
            foreach ($assignToTeamDTO->monthly_target as $index => $target) {
                if ($this->IsAllowMonthlyTarget($target['month'], $target['year'])) {
                    $chair->targets()->create([
                        'period_type' => "monthly",
                        'year' => now()->year,
                        'period_number' => $target['month'],
                        'effective_from' => now()->setMonth((int)$target['month'])->startOfMonth()->format('Y-m-d H:i:s'),
                        'effective_to' => now()->setMonth((int)$target['month'])->endOfMonth()->format('Y-m-d H:i:s'),
                        'target_value' => $target['amount'],
                    ]);
                } else {
                    $monthName = now()->copy()->setMonth((int)$target['month']);
                    $validator->errors()->add($index . ".month", "You are not allowed to set target for before month " . $monthName->format('F Y'));
                }
            }
            if ($assignToTeamDTO->quarterly_target) {
                $this->TotalMonthlyTargetInQuarter($assignToTeamDTO->quarterly_target, $chair, $validator);
            }
        }

        if ($assignToTeamDTO->quarterly_target) {
            foreach ($assignToTeamDTO->quarterly_target as $index => $target) {
                if ($this->IsAllowQuarterlyTarget($target['quarter'])) {
                    // $this->checkIfQuarterlyEqualMonths($target['quarter'], $target['amount'], $assignToTeamDTO->monthly_target, $validator);
                    $chair->targets()->create([
                        'period_type' => "quarterly",
                        'year' => $target['year'],
                        'period_number' => $target['part'],
                        'effective_from' => now()->startOfQuarter(),
                        'effective_to' => now()->endOfQuarter(),
                        'target_value' => $target['amount'],
                    ]);
                } else {
                    $validator->errors()->add($index . ".quarter", "You are not allowed to set target for before quarter " . $target['quarter']);
                }
            }
        }

        if ($validator->errors()->count() > 0) {
            throw new ValidationException($validator);
        }

        return $user;
    }

    public function endAssignment(int $user_id): bool
    {
        $user = $this->findById($user_id);
        if (now()->format('Y-m-d') == $user->activeChair()->first()->started_at->format('Y-m-d')) {
            throw ValidationException::withMessages([
                'team' => 'You cannot end your assignment on the same day you started',
            ]);
        } else {
            $user->activeChair()->update(['ended_at' => now()]);
            $user->update(['team_id' => null]);
            return true;
        }
    }

    public function IsAllowMonthlyTarget($month, $year): bool
    {
        $selectedDate  = now()->copy()->year((int)$year)->month((int)$month)->startOfMonth();
        $minDateAllowed = now()->copy()->startOfMonth();
        return ($selectedDate >= $minDateAllowed);
    }

    public function IsAllowQuarterlyTarget($quarter, $year): bool
    {
        if ($quarter > 4 || $quarter < 1) {
            throw ValidationException::withMessages([
                'quarter' => 'Quarter must be between 1 and 4',
            ]);
        }
        $endDateOfQuarter = match ((int)$quarter) {
            1 => now()->copy()->setYear((int)$year)->setMonth((int)$quarter * 3)->endOfMonth(),
            2 => now()->copy()->setYear((int)$year)->setMonth((int)$quarter * 3)->endOfMonth(),
            3 => now()->copy()->setYear((int)$year)->setMonth((int)$quarter * 3)->endOfMonth(),
            4 => now()->copy()->setYear((int)$year)->setMonth((int)$quarter * 3)->endOfMonth(),
        };
        $maxDateAllowed  = $endDateOfQuarter;
        return (now() < $maxDateAllowed);
    }

    public function checkIfQuarterlyEqualMonths($quarter, $amount, $target, $validator): void
    {
        if ($target) {
            $target_amounts = array_column($target, 'amount', 'month');
            $amount_of_quarter = match ((int)$quarter) {
                1 => ($target_amounts[1] ?? 0) + ($target_amounts[2] ?? 0) + ($target_amounts[3] ?? 0),
                2 => ($target_amounts[4] ?? 0) + ($target_amounts[5] ?? 0) + ($target_amounts[6] ?? 0),
                3 => ($target_amounts[7] ?? 0) + ($target_amounts[8] ?? 0) + ($target_amounts[9] ?? 0),
                4 => ($target_amounts[10] ?? 0) + ($target_amounts[11] ?? 0) + ($target_amounts[12] ?? 0),
            };

            if (($amount_of_quarter !== $amount) && ($amount_of_quarter = 0)) {
                $validator->errors()->add("quarterly_target.$quarter", "The amount of the quarter is not equal to the amount of the monthly targets");
            }
        }
    }

    public function TotalMonthlyTargetInQuarter($quarterly_target, $chair, $validator): void
    {
        $quarterly_target_amounts = array_column($quarterly_target, 'amount', 'quarter');
        foreach ($quarterly_target_amounts as $quarter => $amount) {
            $targets_amount_monthly = $chair->targets()
                ->where('period_type', 'monthly')
                ->where('year', now()->year)
                ->whereIn('period_number', $this->getkeysofmonthlytarget($quarter))
                ->sum('target_value');
            if ($targets_amount_monthly > $amount) {
                $validator->errors()->add("quarterly_target.$quarter", "The amount of the quarter is not equal to the amount of the monthly targets");
            }
        }
    }

    public function getkeysofmonthlytarget($quarterly_target): array
    {
        return match ((int)$quarterly_target) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        };
    }

    public function getDatabaseFields()
    {
        return [
            'first_name' => [
                'label' => 'First Name',
                'required' => true,
                'type' => 'string'
            ],
            'last_name' => [
                'label' => 'Last Name',
                'required' => true,
                'type' => 'string'
            ],
            'email' => [
                'label' => 'Email Address',
                'required' => true,
                'type' => 'email'
            ],
            'job_title' => [
                'label' => 'Business Phone',
                'required' => false,
                'type' => 'string'
            ],
            'team_id' => [
                'label' => 'Mobile Phone',
                'required' => false,
                'type' => 'string'
            ],
            'lang' => [
                'label' => 'Job Title',
                'required' => false,
                'type' => 'string'
            ],
            'last_login_at' => [
                'label' => 'Department',
                'required' => false,
                'type' => 'string'
            ],
            'phone' => [
                'label' => 'Status',
                'required' => false,
                'type' => 'select',
                'options' => ['active', 'inactive', 'pending']
            ],
            'department_id' => [
                'label' => 'Preferred Contact Method',
                'required' => false,
                'type' => 'select',
                'options' => ['email', 'phone', 'whatsapp', 'meeting']
            ],
            'is_active' => [
                'label' => 'Email Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'remember_token' => [
                'label' => 'Phone Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'created_at' => [
                'label' => 'WhatsApp Permission',
                'required' => false,
                'type' => 'boolean'
            ],
            'updated_at' => [
                'label' => 'Company Name',
                'required' => false,
                'type' => 'string'
            ],
            'landlord_id' => [
                'label' => 'Website',
                'required' => false,
                'type' => 'url'
            ],
        ];
    }

    public function getStartAndEndOfQuarter(int $year, int $quarter): array
    {
        $month = ($quarter - 1) * 3 + 1;
        return [
            now()->copy()->year($year)->month($month)->startOfMonth(),
            now()->copy()->year($year)->month($month)->addMonths(2)->endOfMonth(),
        ];
    }
}
