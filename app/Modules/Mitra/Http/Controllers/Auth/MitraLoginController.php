<?php

namespace App\Modules\Mitra\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Menampilkan form login mitra. Autentikasinya sendiri dikerjakan Fortify
 * (POST /login + portal=mitra), bukan controller ini.
 */
final class MitraLoginController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasRole(User::ROLE_MITRA)) {
            return redirect()->route('mitra.dashboard');
        }

        // Sedang login tetapi tanpa akses mitra: jangan tampilkan form (POST /login butuh sesi tamu).
        if ($user !== null) {
            return redirect($user->homeUrl())
                ->with('status', 'Akun ini tidak punya akses mitra aktif. Keluar dulu bila ingin masuk dengan akun mitra lain.');
        }

        return view('mitra::auth.login');
    }
}
