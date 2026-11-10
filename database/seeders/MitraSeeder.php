<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Seeder;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        // Admin platform
        $admin = User::firstOrNew(['email' => 'admin@bexplore.test']);
        $admin->forceFill([
            'name'              => 'Admin B-Explore',
            'password'          => 'password',
            'role'              => 'admin',
            'tenant_id'         => null,
            'email_verified_at' => now(),
        ])->save();

        // Mitra (status & komisi lewat forceFill karena tidak mass-assignable)
        $mitra = Mitra::firstOrNew(['slug' => 'loket-kawah-ijen']);
        $mitra->forceFill([
            'nama'              => 'Loket Wisata Kawah Ijen',
            'email'             => 'loket@kawahijen.test',
            'telepon'           => '081234567890',
            'alamat'            => 'Licin, Banyuwangi, Jawa Timur',
            'deskripsi'         => 'Pengelola tiket dan paket wisata Kawah Ijen.',
            'persentase_komisi' => 10,
            'status'            => 'aktif',
        ])->save();

        // Akun PIC mitra
        $pic = User::firstOrNew(['email' => 'budi@kawahijen.test']);
        $pic->forceFill([
            'name'              => 'Budi Santoso',
            'password'          => 'password',
            'role'              => 'mitra',
            'tenant_id'         => $mitra->id,
            'email_verified_at' => now(),
        ])->save();
    }
}