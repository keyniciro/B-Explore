<?php

namespace App\Modules\Admin\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Menampilkan form login admin. Autentikasinya dikerjakan Fortify (POST /login + portal=admin).
 */
final class AdminLoginController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasRole(User::ROLE_ADMIN)) {
            return redirect()->route('admin.dashboard');
        }

        if ($user !== null) {
            return redirect($user->homeUrl())
                ->with('status', 'Akun ini bukan akun admin. Keluar dulu bila ingin masuk sebagai admin.');
        }

        return view('admin::auth.login');
    }
}