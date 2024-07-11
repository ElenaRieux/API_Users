<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/success', function () {
    return "hey";
});

Route::get('/reset-password/{token}', function (string $token) {
})->middleware('guest')->name('password.reset');
