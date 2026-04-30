<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/verify-2fa', [AuthController::class, 'verifyTwoFactor']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/todo', function () {
        return view('todo');
    });

    Route::get('/todo/nova', function () {
        return view('todo');
    });

    Route::get('/perfil', function () {
        return view('todo');
    });

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('/profile/two-factor', [ProfileController::class, 'updateTwoFactor']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

require __DIR__.'/tasks.php';
