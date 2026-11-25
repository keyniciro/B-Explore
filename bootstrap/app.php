<?php

use App\Support\Auth\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
<<<<<<< HEAD
            'role' => EnsureUserHasRole::class,
        ]);

        // Tamu dikirim ke login portal yang dituju: mitra, admin, atau wisatawan.
        $middleware->redirectGuestsTo(fn (Request $request) => match (true) {
            $request->is('mitra/dashboard*') => route('mitra.login'),
            $request->is('admin', 'admin/*') => route('admin.login'),
            default => route('login'),
        });

        // Tamu yang membuka dashboard mitra dikirim ke login mitra; selain itu ke login wisatawan.
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('mitra/dashboard*')
            ? route('mitra.login')
            : route('login'));

        // Pengguna yang SUDAH login lalu membuka halaman tamu (/login, /register) dikirim ke beranda rolenya.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->homeUrl() ?? route('home'));
=======
        'role'  => \App\Http\Middleware\EnsureUserHasRole::class,
        'mitra' => \App\Http\Middleware\SetMitraContext::class,
    ]);
        //
>>>>>>> kennzac
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
