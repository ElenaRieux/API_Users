<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'hi';
});

Route::get('/reset-password/{token}', function (string $token) {
})->middleware('guest')->name('password.reset');
