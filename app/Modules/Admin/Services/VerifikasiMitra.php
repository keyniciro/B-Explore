<?php

namespace App\Modules\Admin\Services;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Aturan verifikasi mitra oleh super admin. Setiap method mengunci baris mitra dan memeriksa
 * status asal, jadi dua admin yang menekan tombol bersamaan tidak saling menimpa.
 * Mengembalikan false bila status mitra sudah berubah (sudah diproses).
 */
final class VerifikasiMitra
{
    /** pending -> aktif; pemilik naik dari wisatawan menjadi mitra. */
    public function setujui(int $mitraId, User $admin): bool
    {
        return DB::transaction(function () use ($mitraId, $admin): bool {
            $mitra = $this->kunci($mitraId, Mitra::STATUS_PENDING);

            if ($mitra === null) {
                return false;
            }

            $this->tandai($mitra, Mitra::STATUS_AKTIF, null, $admin);

            // role hanya boleh diubah server (bukan input form): query builder melewati fillable.
            $mitra->users()
                ->where('role', User::ROLE_WISATAWAN)
                ->update(['role' => User::ROLE_MITRA]);

            return true;
        });
    }

    /** pending -> ditolak (role pemilik tetap wisatawan). */
    public function tolak(int $mitraId, string $alasan, User $admin): bool
    {
        return DB::transaction(function () use ($mitraId, $alasan, $admin): bool {
            $mitra = $this->kunci($mitraId, Mitra::STATUS_PENDING);

            if ($mitra === null) {
                return false;
            }

            $this->tandai($mitra, Mitra::STATUS_DITOLAK, $alasan, $admin);

            return true;
        });
    }

    /** aktif -> nonaktif (pemilik tetap role mitra, tetapi hasRole('mitra') jadi false). */
    public function nonaktifkan(int $mitraId, User $admin): bool
    {
        return DB::transaction(function () use ($mitraId, $admin): bool {
            $mitra = $this->kunci($mitraId, Mitra::STATUS_AKTIF);

            if ($mitra === null) {
                return false;
            }

            $this->tandai($mitra, Mitra::STATUS_NONAKTIF, null, $admin);

            return true;
        });
    }

    /** nonaktif -> aktif. */
    public function aktifkan(int $mitraId, User $admin): bool
    {
        return DB::transaction(function () use ($mitraId, $admin): bool {
            $mitra = $this->kunci($mitraId, Mitra::STATUS_NONAKTIF);

            if ($mitra === null) {
                return false;
            }

            $this->tandai($mitra, Mitra::STATUS_AKTIF, null, $admin);

            return true;
        });
    }

    private function kunci(int $mitraId, string $statusSyarat): ?Mitra
    {
        $mitra = Mitra::query()->lockForUpdate()->find($mitraId);

        return $mitra !== null && $mitra->status === $statusSyarat ? $mitra : null;
    }

    private function tandai(Mitra $mitra, string $status, ?string $alasan, User $admin): void
    {
        // status tidak fillable (sengaja): hanya server/admin yang boleh mengubahnya.
        $mitra->forceFill([
            'status' => $status,
            'alasan_penolakan' => $alasan,
            'ditinjau_pada' => now(),
            'ditinjau_oleh' => $admin->id,
        ])->save();
    }
}