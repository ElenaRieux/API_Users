<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\CheckPermission;

// Utenti non registrati

Route::group([
    "middleware" => ["guest"],
], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
});

// Utente registrato

Route::group([
    "middleware" => ["auth:api"],
], function () {


    Route::post('logout', [UserController::class, 'logout']);

    // User
    Route::group(['prefix' => 'users'], function () {
        Route::middleware([CheckPermission::class . ':canCreateUser'])->post('/', [UserController::class, 'store']);
        Route::get('/{uuid}', [UserController::class, 'show']);
        Route::put('/{uuid}', [UserController::class, 'update']);
        Route::delete('/{uuid}', [UserController::class, 'destroy']);
        Route::middleware([CheckPermission::class . ':canReadUser'])->get('/', [UserController::class, 'index']);
    });

    // Role
    Route::group(['prefix' => 'roles'], function () {
        Route::middleware([CheckPermission::class . ':canCreateRole'])->post('/', [RoleController::class, 'store']);
        Route::middleware([CheckPermission::class . ':canUpdateRole'])->put('/{uuid}', [RoleController::class, 'update']);
        Route::middleware([CheckPermission::class . ':canReadRole'])->get('/', [RoleController::class, 'index']);
        Route::middleware([CheckPermission::class . ':canReadRole'])->get('/{uuid}', [RoleController::class, 'show']);
        Route::middleware([CheckPermission::class . ':canDeleteRole'])->delete('/{uuid}', [RoleController::class, 'destroy']);
    });

    // Stripe Payment
    Route::post('/stripe', [StripeController::class, 'createCheckoutSession']);
    Route::post('/success', [StripeController::class, 'success'])->name('payment.success');
    Route::post('/cancel', [StripeController::class, 'cancel'])->name('payment.cancel');
});
Route::post('/webhook', [StripeController::class, 'webhook'])->name('payment.webhook');



// Prodotti
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});


// Cambiare password

Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->name('password.update');
