<?php

namespace App\Policies;

use App\Models\MemberRole;
use App\Models\User;
use App\Models\Workspace;

class MemberRolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewFromWorkspace(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner->id
            || $workspace->users->contains($user->id);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id
            || $memberRole->workspace->users->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }
}
