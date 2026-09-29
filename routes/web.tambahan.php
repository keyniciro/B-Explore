<?php

// === Tambahkan ke routes/web.php ===

use App\Http\Controllers\Admin\JenisTiketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('jenis-tiket', JenisTiketController::class)
            ->parameters(['jenis-tiket' => 'jenisTiket'])
            ->except('show');
    });
