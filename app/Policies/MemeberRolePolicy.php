<?php

namespace App\Policies;

use App\Models\MemberRole;
use App\Models\Schema;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class MemeberRolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MemberRole $memberRole): bool
    {
        return $user->id === $memberRole->workspace->owner->id;
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
