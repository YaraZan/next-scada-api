<?php

use App\Http\Controllers\MemberRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchemaController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

/**
 * Users
 */
Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});


/**
 * Roles
 */
Route::resource('roles', RoleController::class)->middleware('auth:sanctum');


/**
 * Workspaces
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(WorkspaceController::class)->group(function () {
        Route::get('/workspaces/shared', 'shared');
        Route::post('/workspaces/share', 'share');
        Route::post('/workspaces/unshare', 'unshare');
    });
    Route::resource('workspaces', WorkspaceController::class);
});

/**
 * Schemas
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/schemas/workspace/{workspace}', [SchemaController::class, 'fromWorkspace']);
    Route::resource('schemas', SchemaController::class);
});


/**
 * Member roles
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/memberRoles/fromWorkspace/{workspaceUuid}', [MemberRoleController::class, 'viewFromWorkspace']);
    Route::resource('memberRoles', MemberRoleController::class);
});
