<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BasesController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Эти маршруты будут доступны только для аутентифицированных пользователей (с API токеном)
    Route::get('/bases', [BasesController::class, 'index']);
    Route::get('/bases/{id}', [BasesController::class, 'show']);
    Route::post('/bases', [BasesController::class, 'store']);
    Route::put('/bases/{id}', [BasesController::class, 'update']);
    Route::delete('/bases/{id}', [BasesController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::get('/orders/user/{id}', [OrderController::class, 'getOrdersByUser']);
    Route::patch('/orders/{id}/process', [OrderController::class, 'process']);
    Route::patch('/orders/{orderId}/tariff/{tariffId}/process', [OrderController::class, 'updateTariffStatus']);
    Route::patch('/orders/{orderId}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{orderId}/pay', [OrderController::class, 'pay']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tariffs', [TariffController::class, 'index']);
    Route::get('/tariffs/{id}', [TariffController::class, 'show']);
    Route::post('/tariffs', [TariffController::class, 'store']);
    Route::put('/tariffs/{id}', [TariffController::class, 'update']);
    Route::delete('/tariffs/{id}', [TariffController::class, 'destroy']);
});

Route::get('/places', [PlaceController::class, 'index']);
Route::get('/places/{id}', [PlaceController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/places', [PlaceController::class, 'store']);
    Route::put('/places/{id}', [PlaceController::class, 'update']);
    Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
});

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


