<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WisatawanAuthController;
use App\Http\Controllers\Auth\MitraAuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth') 
    ->name('dashboard');

Route::get('/login', [WisatawanAuthController::class, 'showLoginForm'])->name('wisatawan.login');
Route::get('/daftar', [WisatawanAuthController::class, 'showRegisterForm'])->name('wisatawan.daftar');
Route::get('/daftar', [WisatawanAuthController::class, 'showRegisterForm'])->name('wisatawan.daftar');
Route::post('/daftar', [WisatawanAuthController::class, 'register']);
Route::get('/login', [WisatawanAuthController::class, 'showLoginForm'])->name('wisatawan.login');
Route::post('/login', [WisatawanAuthController::class, 'login']);

Route::get('/mitra/login', [MitraAuthController::class, 'showLoginForm'])->name('mitra.login');
Route::get('/mitra/daftar', [MitraAuthController::class, 'showRegisterForm'])->name('mitra.daftar');
