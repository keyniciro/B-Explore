<?php

namespace App\Modules\Wisatawan;

use App\Modules\ModuleServiceProvider;

final class WisatawanServiceProvider extends ModuleServiceProvider
{
    protected function moduleAlias(): string
    {
        return 'wisatawan';
    }
}

