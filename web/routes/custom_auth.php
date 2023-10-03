<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CustomAuth\LoginRegisterController;

if(App::environment('local')){
    Route::controller(LoginRegisterController::class)->group(function() {
        Route::get('/register', 'register')->name('register');
        Route::post('/store', 'store')->name('store');
        Route::get('/login', 'login')->name('login');
        Route::post('/authenticate', 'authenticate')->name('authenticate');
        Route::post('/logout', 'logout')->name('logout');
    });
}