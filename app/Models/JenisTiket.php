<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisTiket extends Model
{
    use BelongsToTenant;

    protected $table = 'jenis_tiket';

    protected $fillable = [
        'tenant_id', 'destinasi_id', 'nama', 'deskripsi', 'harga',
        'kuota_harian', 'berlaku_dari', 'berlaku_sampai', 'status',
    ];

    protected $casts = [
        'harga' => 'integer', // rupiah utuh
        'kuota_harian' => 'integer',
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function destinasi(): BelongsTo
    {
        return $this->belongsTo(Destinasi::class, 'destinasi_id');
    }

    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
