<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\PlanScheduleController;
use App\Http\Controllers\API\CropController;
use App\Http\Controllers\API\UserFarmerController;
use App\Http\Controllers\API\BrokerController;
use App\Http\Controllers\API\ChemicalController;
use App\Http\Controllers\API\RequisitionController;
use App\Http\Controllers\API\SysUpdateLogController;
use App\Http\Controllers\API\SupStockController;
use App\Http\Controllers\API\RequisitionItemController;

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
    Route::get('/planschedules', [PlanScheduleController::class, 'index']);
    Route::get('/crop', [CropController::class, 'index']);
    Route::get('/userFarmer', [UserFarmerController::class, 'index']);

Route::middleware(['web','guest'])->post('/auth/login', [AuthController::class, 'login']);
Route::middleware(['web','auth'])->post('auth/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::apiResource('users', UsersController::class);
    Route::apiResource('brokers', BrokerController::class);
    Route::apiResource('chemicals', ChemicalController::class);
    Route::get('/requisitions/broker/{id}', [RequisitionController::class, 'getByBroker']);
    Route::apiResource('requisitions', RequisitionController::class);
    Route::apiResource('requisition-items', RequisitionItemController::class);
    Route::apiResource('sys-log', SysUpdateLogController::class);
    Route::apiResource('sup-stock', SupStockController::class);
});
