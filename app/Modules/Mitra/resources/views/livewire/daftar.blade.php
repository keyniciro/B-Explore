<?php

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::auth', ['lebar' => 'max-w-2xl']), Title('Daftar Mitra')] class extends Component {
    use WithFileUploads;

    public string $nama = '';
    public string $email = '';
    public string $telepon = '';
    public string $alamat = '';
    public string $deskripsi = '';
    public $logo = null;

    public function mount()
    {
        // Sudah punya mitra: tidak perlu mendaftar lagi.
        if (Auth::user()->role === User::ROLE_MITRA) {
            return $this->redirectRoute('mitra.dashboard');
        }
    }

    public function simpan(): void
    {
        $user = Auth::user();

        // Route sudah dijaga, tetapi aksi Livewire adalah endpoint tersendiri: periksa ulang.
        // Yang boleh mendaftar HANYA wisatawan murni; akun yang sudah mitra tidak boleh
        // membuat mitra kedua.
        abort_unless($user?->role === User::ROLE_WISATAWAN, 403);

        $data = $this->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:mitra,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        DB::transaction(function () use ($data, $user): void {
            // Kunci baris user & cek ulang: cegah dua klik/tab menghasilkan dua mitra.
            $terkini = User::query()->lockForUpdate()->findOrFail($user->id);
            abort_unless($terkini->role === User::ROLE_WISATAWAN, 403);

            $mitra = Mitra::create([
                'nama' => $data['nama'],
                'slug' => $this->uniqueSlug($data['nama']),
                'email' => $data['email'],
                'telepon' => $data['telepon'] ?: null,
                'alamat' => $data['alamat'] ?: null,
                'deskripsi' => $data['deskripsi'] ?: null,
                'logo' => $this->logo?->store('mitra-logo', 'public'),
                // status ('pending') dan persentase_komisi (10%) memakai default migrasi.
            ]);

            // role & tenant_id tidak fillable: diisi server dengan forceFill, bukan dari input.
            $terkini->forceFill([
                'role' => User::ROLE_MITRA,
                'tenant_id' => $mitra->id,
            ])->save();
        });

        session()->flash('status', 'Pendaftaran mitra berhasil. Selamat datang di dashboard mitra!');

        $this->redirectRoute('mitra.dashboard');
    }

    private function uniqueSlug(string $nama): string
    {
        $base = Str::slug($nama) ?: 'mitra';
        $slug = $base;
        $i = 1;

        while (Mitra::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}; ?>

<div>
    <h2 class="text-xl font-semibold text-center text-gray-900 mb-1">Daftar Jadi Mitra</h2>
    <p class="text-center text-sm text-gray-500 mb-6">
        Isi data usaha Anda. Setelah berhasil, akun Anda tetap bisa dipakai belanja seperti biasa, dan mendapat tambahan akses kelola mitra.
    </p>

    <form wire:submit="simpan" class="space-y-5">
        <x-explore.field label="Nama Mitra / Usaha" name="nama" wire:model="nama" required autofocus />
        <x-explore.field label="Email Mitra" name="email" type="email" wire:model="email" required />
        <x-explore.field label="Telepon" name="telepon" wire:model="telepon" />
        <x-explore.field label="Alamat" name="alamat" type="textarea" wire:model="alamat" />
        <x-explore.field label="Deskripsi" name="deskripsi" type="textarea" wire:model="deskripsi" />
        <x-explore.field label="Logo (opsional)" name="logo" type="file" wire:model="logo" accept="image/*" />

        <x-explore.button wire:loading.attr="disabled" wire:target="simpan,logo" data-test="daftar-mitra-button">
            <span wire:loading.remove wire:target="simpan">Daftar Mitra</span>
            <span wire:loading wire:target="simpan">Menyimpan…</span>
        </x-explore.button>
    </form>

    <p class="mt-5 text-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="text-emerald-600 hover:underline">← Kembali ke dashboard</a>
    </p>
</div>
