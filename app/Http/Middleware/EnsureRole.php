<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Contoh pemakaian di routes: ->middleware('role:wisatawan')
     * atau untuk beberapa role sekaligus: ->middleware('role:wisatawan,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            return redirect()->route('dashboard')
                ->with('status', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
