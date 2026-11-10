<?php
// app/Models/Mitra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mitra extends Model
{
    protected $table = 'mitra';

    protected $fillable = [
        'nama',
        'slug',
        'email',
        'telepon',
        'alamat',
        'logo',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'persentase_komisi' => 'decimal:2',
        ];
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /** @return HasMany<User, $this> */
    public function pengguna(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}