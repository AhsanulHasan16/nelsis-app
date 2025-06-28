<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthenticatedSessionController::class, 'apiLogin']);
Route::post('register', [RegisteredUserController::class, 'apiRegister']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'apiLogout']);
    Route::get('me', function (Request $request) {
        return response()->json($request->user());
    });

    Route::apiResource('/products', ProductController::class);
});

Route::get('products-public', [ProductController::class, 'publicIndex']);
