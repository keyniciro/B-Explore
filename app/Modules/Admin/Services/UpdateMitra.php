<?php

namespace App\Modules\Admin\Services;

use App\Models\Mitra;
use Illuminate\Support\Arr;

final class UpdateMitra
{
    /**
     * Create a new class instance.
     * @param array<string, mixed> $data
     */
    public function handle(Mitra $mitra, array $data): Mitra
    {
        $mitra->fill(Arr::only($data, ['nama', 'email', 'telepon', 'alamat', 'deskripsi']));
        $mitra->forceFill(['persentase_komisi' => $data['persentase_komisi']])->save();
        //
        return $mitra;
    }
}
