<?php

namespace App\Policies;

use App\Models\Tier;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TierPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view articles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tier $tier): bool
    {
        return $user->can('view articles') &&
            ($tier->is_published || $user->can('view unpublished articles'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->can('create articles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tier $tier): bool
    {
        // Complex logic combining permissions and ownership
        return $user->can('edit articles') &&
            ($user->id === $tier->user_id || $user->hasRole('admin')) &&
            !$tier->is_locked;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tier $tier): bool
    {
        return $user->can('delete articles') &&
            ($user->hasRole('admin') ||
                ($user->id === $tier->user_id && $tier->created_at->diffInHours() < 24));
    }

    public function publish(User $user,  Tier $tier)
    {
        return $user->can('publish articles') &&
            ($user->hasRole(['editor', 'admin']) || $user->id === $tier->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tier $tier): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tier $tier): bool
    {
        return false;
    }
}
