<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoomsController;
use App\Http\Controllers\API\PositionsController;
use App\Http\Controllers\API\EmployeesController;
use App\Http\Controllers\API\EmployeeExternalMapController;
use App\Http\Controllers\API\RoomDefaultsController;
use App\Http\Controllers\API\PlansController;
use App\Http\Controllers\API\PlanItemsController;
use App\Http\Controllers\API\WorksController;
use App\Http\Controllers\API\WorkAssignmentsController;
use App\Http\Controllers\API\WorkEventsController;
use App\Http\Controllers\API\WorkSessionsController;
use App\Http\Controllers\API\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware(['web','guest'])->post('/auth/login', [AuthController::class, 'login']);
Route::middleware(['web','auth'])->post('auth/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::apiResource('rooms', RoomsController::class)->only(['store','update','destroy']);
    Route::apiResource('positions', PositionsController::class)->only(['store','update','destroy']);
    Route::apiResource('employees', EmployeesController::class)->only(['store','update','destroy']);
    Route::apiResource('employee-external-map', EmployeeExternalMapController::class);
    Route::apiResource('room-defaults', RoomDefaultsController::class);
    Route::apiResource('plans', PlansController::class)->only(['store','update','destroy']);
    Route::apiResource('plan-items', PlanItemsController::class);
    Route::apiResource('works', WorksController::class)->only(['store','update','destroy']);
    Route::apiResource('work-assignments', WorkAssignmentsController::class);
    Route::apiResource('work-events', WorkEventsController::class);
    Route::apiResource('work-sessions', WorkSessionsController::class);
    Route::apiResource('users', UsersController::class);
});

