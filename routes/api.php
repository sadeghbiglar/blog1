<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(['/user'=>App\Http\Controllers\API\UserController::class ]);
Route::apiResources(['/centers'=>App\Http\Controllers\API\CentersController::class ]);
Route::apiResources(['/reports'=>App\Http\Controllers\API\ReportsController::class ]);

