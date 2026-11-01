<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun demo untuk pengembangan. Aman dijalankan berulang (idempoten).
     *
     *  wisatawan@example.com / password  -> portal wisatawan
     *  mitra@example.com     / password  -> portal mitra (tenant "Wisata Contoh")
     */
    public function run(): void
    {
        $wisatawan = User::firstOrNew(['email' => 'wisatawan@example.com']);
        $wisatawan->forceFill([
            'name' => 'Budi Wisatawan',
            'password' => 'password',
            'role' => User::ROLE_WISATAWAN,
            'email_verified_at' => now(),
        ])->save();

        $mitra = Mitra::unguarded(fn () => Mitra::firstOrCreate(
            ['slug' => 'wisata-contoh'],
            [
                'nama' => 'Wisata Contoh',
                'email' => 'info@wisata-contoh.test',
                'status' => 'aktif',
            ],
        ));

        $pemilik = User::firstOrNew(['email' => 'mitra@example.com']);
        $pemilik->forceFill([
            'name' => 'Sari Mitra',
            'password' => 'password',
            'role' => User::ROLE_MITRA,
            'tenant_id' => $mitra->id,
            'email_verified_at' => now(),
        ])->save();
    }
}
