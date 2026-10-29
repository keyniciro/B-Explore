<?php 

namespace App\Modules\Admin;

use App\Modules\ModuleServiceProvider;

final class AdminServiceProvider extends ModuleServiceProvider
{
    protected function moduleAlias(): string
    {
        return 'admin';
    }
}