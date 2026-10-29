<?php

namespace App\Modules\Mitra;

use App\Modules\ModuleServiceProvider;

final class MitraServiceProvider extends ModuleServiceProvider
{
    protected function moduleAlias(): string
    {
        return 'mitra';
    }
}