<?php

namespace App\Modules\Admin\Services;

use App\Models\Mitra;
use DomainException;
use Illuminate\Support\Facades\DB;

final class ChangeMitraStatus
{
    public const ALLOWED = ['aktif', 'nonaktif'];

    public function handle(Mitra $mitra, string $status): Mitra
    {
        if (! in_array($status, self::ALLOWED, true)) {
            throw new DomainException('Status mitra tidak dikenal.');
        }

        return DB::transaction(function () use ($mitra, $status): Mitra {
            $locked = Mitra::query()->lockForUpdate()->findOrFail($mitra->id);

            if ($locked->status !== $status) {
                $locked->forceFill(['status' => $status])->save();
            }

            return $locked;
        });
    }
}