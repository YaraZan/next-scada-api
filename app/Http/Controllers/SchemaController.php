<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schema\StoreSchemaRequest;
use App\Http\Requests\Schema\UpdateSchemaRequest;
use App\Models\Schema;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SchemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function fromWorkspace($workspace)
    {
        $workspace = Workspace::findByUuid($workspace);

        if (Gate::allows('viewFromWorkspace', $workspace)) {
            abort(403);
        }

        $schemas = $workspace->schemas()
            ->where(function ($query) {
                $query->whereHas('workspace', function ($query) {
                    $query->where('owner_id', Auth::id()); // Check if the user is the workspace owner
                })
            ->orWhere('creator_id', Auth::id()) // Check if the user is the schema creator
            ->orWhereHas('memberRoles', function ($query) {
                $query->whereHas('users', function ($query) {
                    $query->where('id', Auth::id());
                });
            });
            })
            ->paginate(15);

        return $schemas;
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $schema = Schema::findByUuid($uuid);

        if (Gate::allows('view', $schema)) {
            abort(403);
        }

        return $schema;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchemaRequest $request)
    {
        $validated = $request->validated();
        $workspace = Workspace::findByUuid($validated['workspace']);

        if (Gate::allows('create', $workspace)) {
            abort(403);
        }

        $schema = Schema::make($validated['name']);
        $schema->workspace()->associate($workspace);
        $schema->save();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchemaRequest $request, $uuid)
    {
        $validated = $request->validated();
        $schema = Schema::findByUuid($uuid);

        if (Gate::allows('update', $schema)) {
            abort(403);
        }

        $schema->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($uuid)
    {
        $schema = Schema::findByUuid($uuid);

        if (Gate::allows('delete', $schema)) {
            abort(403);
        }

        $schema->delete();
    }
}
