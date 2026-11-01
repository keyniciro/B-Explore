<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Route /dashboard (wisatawan) dan /mitra/* dimiliki modulnya masing-masing:
// app/Modules/{Modul}/routes/*.php (dimuat otomatis oleh ModuleServiceProvider).

require __DIR__.'/settings.php';