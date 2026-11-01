<?php

use App\Modules\Admin\AdminServiceProvider;
use App\Modules\Mitra\MitraServiceProvider;
use App\Modules\Wisatawan\WisatawanServiceProvider;
use App\Providers\AppServiceProvider;
use App\Support\Auth\AuthServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    MitraServiceProvider::class,
    AdminServiceProvider::class,
    WisatawanServiceProvider::class,
];
