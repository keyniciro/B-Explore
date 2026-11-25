<?php

<<<<<<< HEAD
use App\Modules\Admin\Http\Controllers\Auth\AdminLoginController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

/*
 * Route modul Admin. Login admin memakai endpoint Fortify (POST /login dengan portal=admin);
 * modul ini memiliki halaman login, dashboard, dan verifikasi mitra.
 */

PortalRoutes::publik(function (): void {
    Route::get('/admin/login', AdminLoginController::class)->name('admin.login');
});

// /admin/dashboard → admin.dashboard, /admin/verifikasi-mitra → admin.verifikasi-mitra
PortalRoutes::admin(function (): void {
    Route::livewire('/dashboard', 'admin::dashboard')->name('dashboard');
    Route::livewire('/verifikasi-mitra', 'admin::verifikasi-mitra')->name('verifikasi-mitra');
});
=======
use App\Modules\Admin\Http\Controllers\AdminMitraController;
use App\Modules\Admin\Http\Controllers\AdminMitraStatusController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

PortalRoutes::admin(function (): void {

    Route::get('dashboard', \App\Http\Controllers\DashboardRedirectController::class)->name('dashboard');

    Route::get('/mitra', [AdminMitraController::class, 'index'])->name('mitra.index');
    Route::get('/mitra/create', [AdminMitraController::class, 'create'])->name('mitra.create');
    Route::post('/mitra', [AdminMitraController::class, 'store'])->name('mitra.store');
    Route::get('/mitra/{mitra}', [AdminMitraController::class, 'edit'])->name('mitra.edit');
    Route::put('/mitra/{mitra}', [AdminMitraController::class, 'update'])->name('mitra.update');
    Route::post('/mitra/{mitra}/status', AdminMitraStatusController::class)->name('mitra.status');
});
>>>>>>> kennzac
