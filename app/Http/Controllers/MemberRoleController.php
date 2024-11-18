<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRole\AssignToUserMemberRoleRequest;
use App\Http\Requests\MemberRole\AttachSchemaMemberRoleRequest;
use App\Http\Requests\MemberRole\DetachSchemaMemberRoleRequest;
use App\Http\Requests\MemberRole\StoreMemberRoleRequest;
use App\Http\Requests\MemberRole\UpdateMemberRoleRequest;
use App\Models\MemberRole;
use App\Models\Schema;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemberRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function viewFromWorkspace($workspaceUuid)
    {
        $workspace = Workspace::findByUuid($workspaceUuid);

        if (!Gate::allows('viewFromWorkspace-memberRole', $workspace)) {
            abort(403);
        }

        return $workspace->memberRoles;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRoleRequest $request)
    {
        $validated = $request->validated();
        $workspace = Workspace::findByUuid($validated['workspace']);

        if (!Gate::allows('create', $workspace)) {
            abort(403);
        }

        $memberRole = MemberRole::make($validated);
        $memberRole->workspace()->associate($workspace);
        $memberRole->save();

        if (isset($validated['schemas'])) {
            $schemaIds = Schema::getByUuids($validated['schemas'])->pluck('id')->toArray();

            if (!empty($schemaIds)) {
                foreach ($schemaIds as $id) {
                    $memberRole->schemas()->detach($id);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $memberRole = MemberRole::findByUuid($uuid)->load(['schemas', 'workspace']);

        if (!Gate::allows('view', $memberRole)) {
            abort(403);
        }

        return $memberRole;
    }

    public function attachSchema(AttachSchemaMemberRoleRequest $request)
    {
        $validated = $request->validated();
        $memberRole = MemberRole::findByUuid($validated['member_role']);

        if (!Gate::allows('attachSchema', $memberRole)) {
            abort(403);
        }

        if (isset($validated['schemas'])) {
            $schemaIds = Schema::getByUuids($validated['schemas'])->pluck('id')->toArray();

            if (!empty($schemaIds)) {
                foreach ($schemaIds as $id) {
                    $memberRole->schemas()->attach($id);
                }
            }
        }
    }

    public function detachSchema(DetachSchemaMemberRoleRequest $request)
    {
        $validated = $request->validated();
        $memberRole = MemberRole::findByUuid($validated['member_role']);

        if (!Gate::allows('detachSchema', $memberRole)) {
            abort(403);
        }

        if (isset($validated['schemas'])) {
            $schemaIds = Schema::getByUuids($validated['schemas'])->pluck('id')->toArray();

            if (!empty($schemaIds)) {
                foreach ($schemaIds as $id) {
                    $memberRole->schemas()->detach($id);
                }
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRoleRequest $request, $uuid)
    {
        $validated = $request->validated();
        $memberRole = MemberRole::findByUuid($uuid);

        if (!Gate::allows('update', $memberRole)) {
            abort(403);
        }

        $memberRole->update([
            'name' => $validated['name'] ?? $memberRole->name,
            'color' => $validated['color'] ?? $memberRole->color,
            'description' => $validated['description'] ?? $memberRole->description,
            'can_write_tags' => $validated['can_write_tags'] ?? $memberRole->can_write_tags,
            'can_create_schemas' => $validated['can_create_schemas'] ?? $memberRole->can_create_schemas,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $memberRole = MemberRole::findByUuid($uuid);

        if (!Gate::allows('delete', $memberRole)) {
            abort(403);
        }

        $memberRole->delete();
    }

    public function assignToUser(AssignToUserMemberRoleRequest $request)
    {
        $validated = $request->validated();
        $user = User::findByUuid($validated['user']);
        $workspace = Workspace::findByUuid($validated['workspace']);

        if ($validated['member_role'] !== 'guest') {
            $memberRole = MemberRole::findByUuid($validated['member_role']);

            if (!Gate::allows('assign', [$memberRole, $user])) {
                abort(403);
            }

            $user->workspaces()->syncWithoutDetaching([
                $workspace->id => [
                    'member_role_id' => $memberRole->id
                ]
            ]);
        } else {
            if (!Gate::allows('guestify-memberRole', [$workspace, $user])) {
                abort(403);
            }

            $user->workspaces()->syncWithoutDetaching([
                $workspace->id => [
                    'member_role_id' => null
                ]
            ]);
        }
    }
}
