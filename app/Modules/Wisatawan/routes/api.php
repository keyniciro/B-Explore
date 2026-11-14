<?php

use App\Modules\Wisatawan\Http\Controllers\Api\Auth\LoginController;
use App\Modules\Wisatawan\Http\Controllers\Api\Auth\LogoutController;
use App\Modules\Wisatawan\Http\Controllers\Api\Auth\RegisterController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

PortalRoutes::api(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', RegisterController::class);
        Route::post('/login', LoginController::class);
        Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');
    });
    Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');
});
