<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HolidayPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([], function () {
    Route::post('/login',                                                          [AuthController::class, 'login']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/holiday-plans',                                                    [HolidayPlanController::class, 'getAll']);
    Route::get('/holiday-plan/{id}',                                               [HolidayPlanController::class, 'getById']);
    Route::get('/holiday-plan/{id}/pdf',                                           [HolidayPlanController::class, 'pdfGenerate']);
    Route::post('/holiday-plan',                                                   [HolidayPlanController::class, 'store']);
    Route::put('/holiday-plan/{id}',                                               [HolidayPlanController::class, 'update']);
    Route::delete('/holiday-plan/{id}',                                            [HolidayPlanController::class, 'destroy']);
});




