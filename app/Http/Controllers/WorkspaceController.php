<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspace\ShareWorkspaceRequest;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UnshareWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Models\MemberRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('viewAny', Workspace::class)) {
            abort(403);
        }

        return Workspace::with(['owner'])
        ->where('owner_id', Auth::id())
        ->get();
    }

    /**
     * Display a list of workspaces where user invited.
     */
    public function shared()
    {
        if (!Gate::allows('viewShared', Workspace::class)) {
            abort(403);
        }

        return User::with(['workspaces'])->find(Auth::id())->workspaces;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $validated = $request->validated();

        $workspace = Workspace::make($validated);

        $workspace->owner()->associate(Auth::user());
        $workspace->save();
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $workspace = Workspace::findByUuid($uuid);

        if (!Gate::allows('view', $workspace)) {
            abort(403);
        }

        return $workspace->load(['owner', 'users']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, $uuid)
    {
        $workspace = Workspace::findByUuid($uuid);

        if (!Gate::allows('update', $workspace)) {
            abort(403);
        }

        $validated = $request->validated();

        $workspace->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $workspace = Workspace::findByUuid($uuid);

        if (!Gate::allows('delete', $workspace)) {
            abort(403);
        }

        $workspace->delete();
    }

    public function share(ShareWorkspaceRequest $request)
    {
        $validated = $request->validated();

        $workspace = Workspace::findByUuid($validated['workspace'])->load(['users']);
        $invitedUser = User::findByUuid($validated['user']);

        if (!Gate::allows('share', [$workspace, $invitedUser])) {
            abort(403);
        }

        if ($validated['member_role'] !== 'guest') {
            $memberRole = MemberRole::findByUuid($validated['member_role']);

            $workspace->users()->attach($invitedUser->id, ['member_role_id' => $memberRole->id]);
        } else {
            $workspace->users()->attach($invitedUser->id);
        }
    }

    public function unshare(UnshareWorkspaceRequest $request)
    {
        $validated = $request->validated();

        $workspace = Workspace::findByUuid($validated['workspace'])->load(['users']);
        $invitedUser = User::findByUuid($validated['user']);

        if (!Gate::allows('unshare', [$workspace, $invitedUser])) {
            abort(403);
        }

        $workspace->users()->detach($invitedUser->id);
    }
}
