<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Modules\Mitra\MitraServiceProvider;
use App\Modules\Admin\AdminServiceProvider;
use App\Modules\Wisatawan\WisatawanServiceProvider;


return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    MitraServiceProvider::class,
    AdminServiceProvider::class,
    WisatawanServiceProvider::class,
];
