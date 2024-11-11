<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OptionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/options', [OptionController::class, 'index']);
Route::get('/options/{id}', [OptionController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/options', [OptionController::class, 'store']);
    Route::put('/options/{id}', [OptionController::class, 'update']);
    Route::delete('/options/{id}', [OptionController::class, 'destroy']);
});
