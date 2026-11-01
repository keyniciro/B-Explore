<?php

use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

/*
 * Route modul Wisatawan. Halaman login (/login) dan daftar (/register) ditangani Laravel Fortify;
 * tampilannya dari modul ini (lihat FortifyServiceProvider::configureViews()).
 * Middleware auth + verified + role:wisatawan berasal dari PortalRoutes::wisatawan().
 */
PortalRoutes::wisatawan(function (): void {
    Route::livewire('/dashboard', 'wisatawan::dashboard')->name('dashboard');
});
