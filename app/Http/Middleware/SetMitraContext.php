<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\MitraContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetMitraContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isMitra()) {
            abort(403);
        }

        $mitra = $user->mitra;

        // mitra pending/nonaktif tidak boleh masuk walau sesi lama masih ada
        if ($mitra === null || ! $mitra->isAktif()) {
            abort(403);
        }

        $context = app(MitraContext::class);
        $context->set($mitra);

        try {
            return $next($request);
        } finally {
            $context->clear();
        }
    }
}