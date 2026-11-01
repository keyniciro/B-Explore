<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tenant = mitra wisata. Model ditaruh di shared kernel (app/Models) karena satu database
 * dipakai lintas modul.
 *
 * `status` dan `persentase_komisi` SENGAJA tidak fillable: keduanya hanya boleh diubah oleh
 * admin lewat forceFill()/service, bukan lewat input form pendaftaran.
 */
#[Fillable(['nama', 'slug', 'email', 'telepon', 'alamat', 'logo', 'deskripsi'])]
class Mitra extends Model
{
    protected $table = 'mitra';

    protected function casts(): array
    {
        return [
            'persentase_komisi' => 'decimal:2',
        ];
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}
