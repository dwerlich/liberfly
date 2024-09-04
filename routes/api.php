<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::get('teste', function (Request $request) {
    echo 'ping';
});

// Rotas com prefixo v1 protegidas pelo middleware sanctum
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::resource('events', EventController::class);
});
