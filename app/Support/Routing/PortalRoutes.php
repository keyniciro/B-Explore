<?php

namespace App\Support\Routing;

use Closure;
use Illuminate\Support\Facades\Route;

final class PortalRoutes
{
    public static function admin(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group($routes);
    }

    public static function mitra(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:mitra', 'mitra'])
            ->prefix('mitra')
            ->name('mitra.')
            ->group($routes);
    }
}