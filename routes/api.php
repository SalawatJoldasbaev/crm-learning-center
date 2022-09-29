<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signIn', [AuthController::class, 'signIn']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employees', [AuthController::class, 'register']);
    Route::prefix('/teachers')
        ->controller(TeacherController::class)
        ->group(function () {
            Route::post('/', 'create');
        });
});
