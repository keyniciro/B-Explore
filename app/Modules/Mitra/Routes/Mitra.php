<?php
use App\Support\Routing\PortalRoutes;
use Illuminate\Support\Facades\Route;

PortalRoutes::mitra(function (): void {
    Route::view('/dashboard', 'mitra::dashboard')->name('dashboard');
});