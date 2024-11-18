<?php

namespace App\Policies;

use App\Models\Schema;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class SchemaPolicy
{
    /**
     * Determine whether the user can view schemas from workspace.
     *
     * Allowed to:
     * - Owner of workspace
     * - Workspace member
     */
    public function viewFromWorkspace(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id
            || $workspace->users
            ->where('user_id', $user->id)
            ->isNotEmpty()
            && !is_null($user->memberRoleInWorkspace($workspace));
    }

    /**
     * Determine whether the user can view the schema.
     *
     * Allowed to:
     * - Owner of workspace
     * - Schema creator
     * - User with a member role that allows viewing this schema
     */
    public function view(User $user, Schema $schema): bool
    {
        return $schema->workspace->owner_id === $user->id
            || $schema->creator_id === $user->id
            || $schema->workspace->memberRoles()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->whereHas('schemas', function ($query) use ($schema) {
                $query->where('id', $schema->id);
            })
            ->exists();
    }


    /**
     * Determine whether the user can create models.
     *
     * Allowed to:
     * - Owner of workspace
     * - User with a member role that allows creating schemas
     */
    public function create(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id
            || $workspace->memberRoles
            ->where('user_id', $user->id)
            ->where('can_create_schemas', true)
            ->isNotEmpty();
    }

    /**
     * Determine whether the user can update the model.
     *
     * Allowed to:
     * - Owner of workspace
     * - Schema creator
     */
    public function update(User $user, Schema $schema): bool
    {
        return $schema->creator_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Allowed to:
     * - Owner of workspace
     * - Schema creator
     */
    public function delete(User $user, Schema $schema): bool
    {
        return $schema->workspace->owner_id === $user->id
            || $schema->creator_id === $user->id;
    }
}
