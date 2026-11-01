<?php

namespace App\Support\Auth;

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

/**
 * Aturan autentikasi yang dipakai BERSAMA oleh portal wisatawan dan mitra (lintas modul):
 * verifikasi login + lockout, redirect setelah login/logout, rate limiter, dan view bawaan
 * starter (verifikasi email, lupa password, dll).
 *
 * Yang dimiliki satu modul ada di provider modulnya: view login/daftar wisatawan dan
 * pembuatan user -> WisatawanServiceProvider.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Redirect setelah login/logout dibedakan per role/portal.
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureAuthentication();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }

    /**
     * Verifikasi kredensial + hak akses portal asal login.
     *
     * - Surel tak dikenal / sandi salah ditolak dengan PESAN YANG SAMA (agar keberadaan akun
     *   tidak terbocor) dan menambah hitungan gagal.
     * - 5x gagal / 10 menit => akun terkunci 15 menit (LoginLockout).
     * - Login wisatawan dan login mitra memakai endpoint Fortify yang sama; field `portal`
     *   menentukan hak yang diperiksa. Akun mitra BOLEH masuk lewat portal wisatawan
     *   (mitra = wisatawan + hak kelola). Pesan "tidak punya akses mitra" baru muncul
     *   SETELAH sandi benar, jadi tidak membocorkan apa pun ke penebak sandi.
     *
     * Catatan: callback ini dipanggil dua kali pada login sukses oleh pipeline Fortify,
     * jadi isinya harus idempoten.
     */
    private function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function (Request $request): User {
            $lockout = app(LoginLockout::class);
            $email = (string) $request->input(Fortify::username());

            if ($lockout->isLocked($email)) {
                throw ValidationException::withMessages([
                    Fortify::username() => 'Akun terkunci sementara karena terlalu banyak percobaan gagal. Coba lagi dalam '.$lockout->minutesRemaining($email).' menit.',
                ]);
            }

            $user = User::query()->where('email', $email)->first();

            if ($user !== null && $user->isActive() && Hash::check((string) $request->input('password'), $user->password)) {
                $portal = $request->input('portal') === 'mitra'
                    ? User::ROLE_MITRA
                    : User::ROLE_WISATAWAN;

                if (! $user->hasRole($portal)) {
                    throw ValidationException::withMessages([
                        Fortify::username() => $portal === User::ROLE_MITRA
                            ? 'Akun ini tidak punya akses mitra. Masuk sebagai wisatawan, lalu pilih Daftar Mitra di dashboard (atau hubungi admin bila mitra Anda dinonaktifkan).'
                            : 'Akun ini tidak dapat masuk lewat halaman ini.',
                    ]);
                }

                $lockout->clear($email);

                return $user;
            }

            $lockout->recordFailure($email);

            throw ValidationException::withMessages([
                Fortify::username() => 'Surel atau kata sandi tidak sesuai.',
            ]);
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        // Login & daftar wisatawan didaftarkan oleh WisatawanServiceProvider (modul pemiliknya).
        Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            // Lapis per-IP terhadap brute force massal; penguncian per akun ditangani LoginLockout.
            return Limit::perMinute(20)->by($request->ip() ?? 'unknown');
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
