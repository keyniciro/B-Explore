<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MitraController extends Controller
{
    /**
     * Tampilkan form pendaftaran mitra.
     * Hanya bisa diakses user yang sudah login sebagai wisatawan (lihat middleware di routes).
     */
    public function create()
    {
        return view('auth.tenant.daftar');
    }

    /**
     * Simpan data mitra baru, lalu upgrade role user yang sedang login menjadi "mitra".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:mitra,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Buat slug otomatis dari nama mitra, pastikan unik.
        $slug = Str::slug($validated['nama']);
        $baseSlug = $slug;
        $suffix = 1;
        while (Mitra::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug.'-'.$suffix;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('mitra-logo', 'public');
        }

        $mitra = Mitra::create([
            'nama' => $validated['nama'],
            'slug' => $slug,
            'email' => $validated['email'],
            'telepon' => $validated['telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'logo' => $logoPath,
            // persentase_komisi & status pakai default dari migration (10.00 & pending)
        ]);

        $user = $request->user();
        $user->role = 'mitra';
        $user->tenant_id = $mitra->id;
        $user->save();

        return redirect()->route('dashboard')
            ->with('status', 'Selamat! Pendaftaran mitra berhasil. Akun Anda sekarang punya akses dashboard mitra.');
    }
}
