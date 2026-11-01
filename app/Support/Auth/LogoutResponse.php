<?php

namespace App\Support\Auth;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Setelah logout: form logout mengirim field `portal`. Dari portal mitra kembali ke
 * /mitra/login, selain itu ke halaman awal. (Saat response dibuat, user sudah logout,
 * jadi asalnya tidak bisa dibaca dari role.)
 */
final class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect($request->input('portal') === 'mitra'
            ? route('mitra.login')
            : route('home'));
    }
}
