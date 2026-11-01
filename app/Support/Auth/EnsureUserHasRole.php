<?php

namespace App\Support\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pemakaian: ->middleware('role:wisatawan') atau ->middleware('role:mitra,admin').
 * Dijalankan SETELAH auth. Role salah atau akun nonaktif => 403.
 */
final class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless($request->user()?->hasRole(...$roles), 403);

        return $next($request);
    }
}
