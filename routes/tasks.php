<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::middleware('auth')->group(function () {
	Route::get('/tasks', [TaskController::class, 'index']);
	Route::post('/tasks', [TaskController::class, 'store']);
	Route::get('/tasks/{task}', [TaskController::class, 'show']);
	Route::post('/tasks/{task}/share', [TaskController::class, 'share']);
	Route::put('/tasks/{task}', [TaskController::class, 'update']);
	Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});
