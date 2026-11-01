<?php

namespace App\Support\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Setelah login berhasil:
 *  - login lewat form mitra  -> /mitra/dashboard
 *  - login lewat form lain   -> /dashboard (mitra pun mendarat di sini; ia wisatawan + hak kelola)
 *
 * URL tujuan semula (intended) hanya dipakai bila cocok dengan portal yang dimasuki, supaya
 * kunjungan tamu sebelumnya (mis. /dashboard) tidak membelokkan login mitra, dan sebaliknya.
 */
final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        /** @var User $user */
        $user = $request->user();
        $viaMitra = $request->input('portal') === 'mitra';

        $intended = (string) $request->session()->get('url.intended', '');
        $path = ltrim((string) parse_url($intended, PHP_URL_PATH), '/');
        $isMitraUrl = $path === 'mitra' || str_starts_with($path, 'mitra/');

        $dipakai = $viaMitra
            ? $isMitraUrl
            : (! $isMitraUrl || $user->hasRole(User::ROLE_MITRA));

        if (! $dipakai) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended($viaMitra ? route('mitra.dashboard') : $user->homeUrl());
    }
}
