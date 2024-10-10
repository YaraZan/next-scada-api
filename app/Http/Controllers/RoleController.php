<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('viewAny', Role::class)) {
            abort(403);
        }

        return Role::all();
    }

    public function show($uuid)
    {
        if (!Gate::allows('view', Role::class)) {
            abort(403);
        }

        return Role::findByUuid($uuid);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        Role::create($validated);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $uuid)
    {
        $role = Role::findByUuid($uuid);
        $validated = $request->validated();

        $role->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $role = Role::findByUuid($uuid);
        $role->delete();
    }
}
