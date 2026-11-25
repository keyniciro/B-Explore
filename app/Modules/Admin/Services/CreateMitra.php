<?php

namespace App\Modules\Admin\Services;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


final class CreateMitra
{
    /** @param array<string, mixed> $data */
    public function handle(array $data): Mitra
    {
        return DB::transaction(function() use ($data): Mitra{
            $mitra = (new Mitra)->forceFill([
                'nama'              => $data['nama'],
                'slug'              => $this->uniqueSlug($data['nama']),
                'email'             => $data['email'],
                'telepon'           => $data['telepon'] ?? null,
                'alamat'            => $data['alamat'] ?? null,
                'deskripsi'         => $data['deskripsi'] ?? null,
                'persentase_komisi' => $data['persentase_komisi'],
                'status'            => 'aktif',
            ]);
            $mitra->save();

            $pic = (new User)->forceFill([
                'name'              => $data['pic_nama'],
                'email'             => $data['pic_email'],
                'password'          => Str::password(32),
                'role'              => 'mitra',
                'tenant_id'         => $mitra->id,
                'email_verified_at' => now(),
            ]);
            $pic->save();

            DB::afterCommit(fn () => Password::broker()->sendResetLink(['email' => $pic->email]));
            return $mitra;
        });
        //
    }

    private function uniqueSlug(string $nama): string
    {
        $base = Str::slug($nama) ?: 'mitra';
        $slug = $base;
        $i = 2;

        while (Mitra::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
