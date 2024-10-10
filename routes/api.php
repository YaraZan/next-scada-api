<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Auth routes
 */
Route::controller(UserController::class)->group(function () {
    Route::post('/users/register', 'register');
    Route::post('/users/login', 'login');
});

Route::resource('roles', RoleController::class)->middleware('auth:sanctum');
