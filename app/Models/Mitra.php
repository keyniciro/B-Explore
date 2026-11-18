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
 *
 * Siklus status:
 *   pending  -> aktif     (admin menyetujui)
 *   pending  -> ditolak   (admin menolak + alasan)
 *   ditolak  -> pending   (pemohon mengajukan ulang)
 *   aktif    <-> nonaktif (admin menonaktifkan / mengaktifkan kembali)
 */
#[Fillable(['nama', 'slug', 'email', 'telepon', 'alamat', 'logo', 'deskripsi'])]
class Mitra extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'mitra';

    protected function casts(): array
    {
        return [
            'persentase_komisi' => 'decimal:2',
            'ditinjau_pada' => 'datetime',
        ];
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}