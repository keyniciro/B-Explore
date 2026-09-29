<?php

namespace App\Models\Concerns;

use App\Models\Mitra;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        // Otomatis isi tenant_id dari staff mitra yang sedang login.
        static::creating(function ($model): void {
            $user = Auth::user();

            if (empty($model->tenant_id) && $user && $user->tenant_id) {
                $model->tenant_id = $user->tenant_id;
            }
        });
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'tenant_id');
    }
}
