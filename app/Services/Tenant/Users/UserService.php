<?php

namespace App\Services\Tenant\Users;

use App\DTO\Tenant\UserDTO;
use App\Models\Tenant\User;
use App\QueryFilters\Tenant\UsersFilters;
use App\Services\BaseService;
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
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations)->orderBy('id','desc');
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
        return $user->load('roles');
    }

    public function update(UserDTO $userDTO, $id)
    {
        $user = $this->findById($id);
        $data = $userDTO->toArray();

        // Remove role from data before updating user
        $role = $data['role'] ?? null;

        if (!isset($data['password']))
            $user->update(Arr::except($data, ['password','role']));
        else
            $user->update(Arr::except($data, ['role']));

        // Handle role assignment
            if ($role) {
                // Remove existing roles and assign new one
                $user->syncRoles([$role]);
        
        }

        return true;
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
}
