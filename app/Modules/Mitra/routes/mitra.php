<?php

use App\Modules\Mitra\Http\Controllers\Auth\MitraLoginController;
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

/*
 * Route modul Mitra. Pemrosesan login mitra memakai endpoint Fortify (POST /login dengan
 * portal=mitra); modul ini hanya memiliki halaman dan dashboard-nya.
 */

// Halaman tamu: form login mitra.
PortalRoutes::publik(function (): void {
    Route::get('/mitra/login', MitraLoginController::class)->name('mitra.login');
});

// Pendaftaran mitra: hanya untuk wisatawan yang sudah login & terverifikasi.
PortalRoutes::wisatawan(function (): void {
    Route::livewire('/mitra/daftar', 'mitra::daftar')->name('mitra.daftar');
});

// Dashboard mitra: /mitra/dashboard, nama route mitra.dashboard.
PortalRoutes::mitra(function (): void {
    Route::livewire('/dashboard', 'mitra::dashboard')->name('dashboard');
});
