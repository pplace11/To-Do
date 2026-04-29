<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

require __DIR__.'/tasks.php';
