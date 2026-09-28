<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MitraAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.tenant.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        // Halaman ini khusus akun yang rolenya sudah "mitra".
        if (Auth::user()->role !== 'mitra') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun ini belum terdaftar sebagai mitra.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // Logout memakai method yang sama dengan wisatawan (lihat routes/web.php),
    // jadi tidak perlu didefinisikan ulang di sini.
}
