<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $fillable = [
        'nama',
        'slug',
        'email',
        'telepon',
        'alamat',
        'logo',
        'deskripsi',
        'persentase_komisi',
        'status', // pending | aktif | nonaktif
    ];

    protected function casts(): array
    {
        return [
            'persentase_komisi' => 'decimal:2',
        ];
    }

    /**
     * User-user (pemilik/staf) yang tergabung ke mitra ini.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}
