<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRole\StoreMemberRoleRequest;
use App\Http\Requests\MemberRole\UpdateMemberRoleRequest;
use App\Models\MemberRole;
use App\Models\Schema;
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
        $workspace = Workspace::findByUuid($validated->workspace);

        if (!Gate::allows('create', $workspace)) {
            abort(403);
        }

        $memberRole = MemberRole::make($validated);
        $memberRole->workspace()->associate($workspace);
        $memberRole->save();

        $schemaIds = Schema::whereIn('uuid', $validated->schemas)->pluck('id');
        $memberRole->schemas()->attach($schemaIds);
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

        $memberRole->fill([
            'name' => $validated['name'] ?? $memberRole->name,
            'color' => $validated['color'] ?? $memberRole->color,
            'description' => $validated['description'] ?? $memberRole->description,
            'can_write_tags' => $validated['can_write_tags'] ?? $memberRole->can_write_tags,
            'can_create_schemas' => $validated['can_create_schemas'] ?? $memberRole->can_create_schemas,
        ]);

        if (isset($validated['schemas'])) {
            $schemaIds = Schema::whereIn('uuid', $validated['schemas'])->pluck('id')->toArray();
            $memberRole->schemas()->sync($schemaIds);
        }

        $memberRole->save();
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
}
