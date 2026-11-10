<?php

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
