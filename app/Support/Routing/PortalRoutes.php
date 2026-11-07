<?php

namespace App\Support\Routing;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Route;

/**
 * Satu-satunya tempat middleware portal didefinisikan. File route modul
 * (app/Modules/{Modul}/routes/*.php) wajib dibungkus salah satu method di sini,
 * sehingga middleware auth/verified/role tidak bisa terlupa.
 */
final class PortalRoutes
{
    /** Halaman publik/tamu (hanya grup 'web'). */
    public static function publik(Closure $routes): void
    {
        Route::middleware('web')->group($routes);
    }

    /** API stateless (token Sanctum): prefix /api, memakai grup middleware 'api' (tanpa sesi/CSRF). */
    public static function api(Closure $routes): void
    {
        Route::middleware('api')->prefix('api')->group($routes);
    }

    /** Portal wisatawan: harus login, email terverifikasi, role wisatawan. */
    public static function wisatawan(Closure $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:'.User::ROLE_WISATAWAN])
            ->group($routes);
    }

    /** Portal mitra: prefix /mitra, nama route mitra.* */
    public static function mitra(Closure $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:'.User::ROLE_MITRA])
            ->prefix('mitra')
            ->name('mitra.')
            ->group($routes);
    }

    /** Portal admin: prefix /admin, nama route admin.* */
    public static function admin(Closure $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'role:'.User::ROLE_ADMIN])
            ->prefix('admin')
            ->name('admin.')
            ->group($routes);
    }
}
