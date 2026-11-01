<?php

namespace App\Modules\Wisatawan;

use App\Modules\ModuleServiceProvider;
use App\Modules\Wisatawan\Actions\CreateNewUser;
use Laravel\Fortify\Fortify;

final class WisatawanServiceProvider extends ModuleServiceProvider
{
    protected function moduleAlias(): string
    {
        return 'wisatawan';
    }

    public function boot(): void
    {
        // Wajib: memuat view, namespace Livewire, dan route modul ini.
        parent::boot();

        // Login & daftar wisatawan: tampilan dan pembuatan akun dimiliki modul ini.
        // (Aturan login bersama dengan mitra ada di App\Support\Auth\AuthServiceProvider.)
        Fortify::loginView(fn () => view('wisatawan::auth.login'));
        Fortify::registerView(fn () => view('wisatawan::auth.register'));
        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
