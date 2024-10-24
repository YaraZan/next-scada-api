<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

/**
 * Users
 */
Route::controller(UserController::class)->group(function () {
    Route::post('/users/register', 'register');
    Route::post('/users/login', 'login');
});


/**
 * Roles
 */
Route::resource('roles', RoleController::class)->middleware('auth:sanctum');


/**
 * Workspaces
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('workspaces', WorkspaceController::class);
    Route::get('/workspaces/shared', [WorkspaceController::class, 'shared']);
    Route::post('/workspaces/share', [WorkspaceController::class, 'share']);
    Route::post('/workspaces/unshare', [WorkspaceController::class, 'unshare']);
});

