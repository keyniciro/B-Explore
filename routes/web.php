<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WisatawanAuthController;
use App\Http\Controllers\Auth\MitraAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\JenisTiketController;

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


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('jenis-tiket', JenisTiketController::class)
            ->parameters(['jenis-tiket' => 'jenisTiket'])
            ->except('show');
    });

Route::get('/dev-login/{id}', function ($id) {
    abort_unless(app()->environment('local'), 404);
    \Illuminate\Support\Facades\Auth::loginUsingId($id);
    return redirect('/admin/jenis-tiket');
});
