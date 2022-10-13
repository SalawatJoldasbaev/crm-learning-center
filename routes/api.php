<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::post('/signIn', [AuthController::class, 'signIn']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employees', [AuthController::class, '']);
    Route::prefix('/employees')
        ->controller(EmployeeController::class)
        ->group(function () {
            Route::get('/', 'ShowAllEmployees');
            Route::post('/', [AuthController::class, 'register']);
        });
    Route::prefix('/teachers')
        ->controller(TeacherController::class)
        ->group(function () {
            Route::post('/', 'create');
            Route::get('/', 'ShowAllTeachers');
        });
    Route::prefix('/students')
        ->controller(StudentController::class)
        ->group(function () {
            Route::post('/', 'createStudent');
            Route::get('/', 'ShowAllStudents');
        });
    Route::prefix('/courses')
        ->controller(CourseController::class)
        ->group(function () {
            Route::post('/', 'createCourse');
            Route::get('/', 'ShowAllCourses');
        });
    Route::prefix('/rooms')
        ->controller(RoomController::class)
        ->group(function () {
            Route::post('/', 'createRoom');
            Route::get('/', 'ShowAllRooms');
        });

    Route::prefix('/groups')
        ->controller(GroupController::class)
        ->group(function () {
            Route::post('/', 'createGroup');
            Route::get('/', 'ShowAllGroups');
        });
});
