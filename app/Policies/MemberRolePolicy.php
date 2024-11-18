<?php

namespace App\Policies;

use App\Models\MemberRole;
use App\Models\User;
use App\Models\Workspace;

class MemberRolePolicy
{
    /**
     * Determine whether the user can view any models in the workspace.
     */
    public function viewFromWorkspace(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner->id
            || $workspace->users->contains($user->id);
    }

    /**
     * Determine whether the user can view the member role model.
     */
    public function view(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id
            || $memberRole->workspace->users->contains($user->id);
    }

    /**
     * Determine whether the user can create models in the workspace.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can attach a schema to the member role.
     */
    public function attachSchema(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }

    /**
     * Determine whether the user can detach a schema from the member role.
     */
    public function detachSchema(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }

    /**
     * Determine whether the user can update the member role model.
     */
    public function update(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }

    /**
     * Determine whether the user can delete the member role model.
     */
    public function delete(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
    }

    /**
     * Determine whether the owner can assign the member role to the user.
     */
    public function assign(User $owner, MemberRole $memberRole, User $user): bool
    {
        return $owner->id === $memberRole->workspace->owner->id
            && $memberRole->workspace->users->contains($user->id);
    }

    /**
     * Determine whether the owner can set the user as a guest (remove their member role).
     */
    public function guestify(User $owner, Workspace $workspace, User $user): bool
    {
        return $owner->id === $workspace->owner->id
            && $workspace->users->contains($user->id);
    }
}
