<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Middleware\CheckPermission;


Route::group([
    "middleware" => ["guest"],
], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
});

Route::group([
    "middleware" => ["auth:api"],
], function () {

    // User

    Route::post('logout', [UserController::class, 'logout']);
    Route::middleware([CheckPermission::class . ':canCreateUser'])->post('/users', [UserController::class, 'store']);
    Route::get('/users/{uuid}', [UserController::class, 'show']);
    Route::put('/users/{uuid}', [UserController::class, 'update']);
    Route::delete('/users/{uuid}', [UserController::class, 'destroy']);
    Route::middleware([CheckPermission::class . ':canReadUser'])->get('/users', [UserController::class, 'index']);

    // Role

    Route::middleware([CheckPermission::class . ':canCreateRole'])->post('/roles', [RoleController::class, 'store']);
    Route::middleware([CheckPermission::class . ':canUpdateRole'])->put('/roles/{uuid}', [RoleController::class, 'update']);
    Route::middleware([CheckPermission::class . ':canReadRole'])->get('/roles', [RoleController::class, 'index']);
    Route::middleware([CheckPermission::class . ':canReadRole'])->get('/roles/{uuid}', [RoleController::class, 'show']);
    Route::middleware([CheckPermission::class . ':canDeleteRole'])->delete('/roles/{uuid}', [RoleController::class, 'destroy']);

});

Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail']);

Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->name('password.update');
