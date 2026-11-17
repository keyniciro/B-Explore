<?php

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