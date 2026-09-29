<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destinasi extends Model
{
    protected $table = 'destinasi';

    protected $fillable = [
        'kategori_id', 'nama', 'slug', 'deskripsi', 'alamat',
        'latitude', 'longitude', 'jam_buka', 'jam_tutup', 'status',
    ];
}
