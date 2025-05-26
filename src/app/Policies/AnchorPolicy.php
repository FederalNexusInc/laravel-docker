<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Anchor;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnchorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_anchor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Anchor $anchor): bool
    {
        return $user->can('view_anchor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_anchor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Anchor $anchor): bool
    {
        return $user->can('update_anchor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Anchor $anchor): bool
    {
        return $user->can('delete_anchor');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_anchor');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Anchor $anchor): bool
    {
        return $user->can('force_delete_anchor');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_anchor');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Anchor $anchor): bool
    {
        return $user->can('restore_anchor');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_anchor');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Anchor $anchor): bool
    {
        return $user->can('replicate_anchor');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_anchor');
    }
}
