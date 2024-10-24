<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user should be able to see a list of workspaces they belong to
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        // User can view the workspace if they are the owner or an invited user
        return $user->id === $workspace->owner->id || $workspace->users->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a workspace
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        // Only the owner can update the workspace
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        // Only the owner can delete the workspace
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Workspace $workspace): bool
    {
        // Only the owner can restore a soft-deleted workspace
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Workspace $workspace): bool
    {
        // Only the owner can permanently delete the workspace
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the authenticated user can share the workspace with the invited user.
     */
    public function share(User $authUser, Workspace $workspace, User $invitedUser): bool
    {
        return $authUser->id === $workspace->owner->id && !$workspace->users->contains($invitedUser->id);
    }

    /**
     * Determine whether the authenticated user can unshare the workspace with the invited user.
     */
    public function unshare(User $authUser, Workspace $workspace, User $invitedUser): bool
    {
        return $authUser->id === $workspace->owner->id && $workspace->users->contains($invitedUser->id);
    }
}
