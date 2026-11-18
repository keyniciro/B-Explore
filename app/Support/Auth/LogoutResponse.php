<?php

namespace App\Support\Auth;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Setelah logout: form logout mengirim field `portal`. Dari portal mitra kembali ke
 * /mitra/login, dari portal admin ke /admin/login, selain itu ke halaman awal.
 */
final class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect(match ($request->input('portal')) {
            'mitra' => route('mitra.login'),
            'admin' => route('admin.login'),
            default => route('home'),
        });
    }
}