<?php

use App\Models\Mitra;
use App\Models\User;
use App\Modules\Admin\Services\VerifikasiMitra;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::portal'), Title('Verifikasi Mitra')] class extends Component {
    use WithPagination;

    /** Tab yang aktif: pending | aktif | ditolak | nonaktif */
    #[Url(as: 'status')]
    public string $filter = 'pending';

    public ?int $tolakId = null;
    public string $alasan = '';
    public string $pesan = '';
    public string $jenisPesan = 'sukses';

    public function updatedFilter(): void
    {
        $this->resetPage();
        $this->batalTolak();
    }

    public function daftarMitra()
    {
        $this->otorisasi();

        $status = in_array($this->filter, ['pending', 'aktif', 'ditolak', 'nonaktif'], true)
            ? $this->filter
            : Mitra::STATUS_PENDING;

        return Mitra::query()->with('users')->where('status', $status)->latest()->paginate(10);
    }

    public function setujui(int $id): void
    {
        $this->otorisasi();
        $berhasil = app(VerifikasiMitra::class)->setujui($id, Auth::user());
        $this->umpan($berhasil, 'Mitra disetujui. Pemiliknya sekarang bisa masuk ke portal mitra.');
    }

    public function mulaiTolak(int $id): void
    {
        $this->otorisasi();
        $this->tolakId = $id;
        $this->alasan = '';
        $this->resetErrorBag();
    }

    public function batalTolak(): void
    {
        $this->tolakId = null;
        $this->alasan = '';
        $this->resetErrorBag();
    }

    public function tolak(): void
    {
        $this->otorisasi();

        $this->validate(
            ['alasan' => ['required', 'string', 'min:10', 'max:500']],
            [
                'required' => ':attribute wajib diisi.',
                'min' => ':attribute minimal :min karakter.',
                'max' => ':attribute maksimal :max karakter.',
            ],
            ['alasan' => 'Alasan penolakan'],
        );

        if ($this->tolakId === null) {
            return;
        }

        $berhasil = app(VerifikasiMitra::class)->tolak($this->tolakId, trim($this->alasan), Auth::user());
        $this->batalTolak();
        $this->umpan($berhasil, 'Pengajuan ditolak. Pemohon akan melihat alasannya di dashboard dan bisa mengajukan ulang.');
    }

    public function nonaktifkan(int $id): void
    {
        $this->otorisasi();
        $berhasil = app(VerifikasiMitra::class)->nonaktifkan($id, Auth::user());
        $this->umpan($berhasil, 'Mitra dinonaktifkan.');
    }

    public function aktifkan(int $id): void
    {
        $this->otorisasi();
        $berhasil = app(VerifikasiMitra::class)->aktifkan($id, Auth::user());
        $this->umpan($berhasil, 'Mitra diaktifkan kembali.');
    }

    // Aksi Livewire adalah endpoint tersendiri: periksa ulang hak admin di tiap aksi.
    private function otorisasi(): void
    {
        abort_unless(Auth::user()?->hasRole(User::ROLE_ADMIN), 403);
    }

    private function umpan(bool $berhasil, string $sukses): void
    {
        $this->pesan = $berhasil
            ? $sukses
            : 'Status mitra sudah berubah (mungkin diproses admin lain). Daftar sudah dimuat ulang.';
        $this->jenisPesan = $berhasil ? 'sukses' : 'gagal';
    }
}; ?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi Mitra</h1>
        <p class="text-gray-500 mt-1">Tinjau pengajuan wisatawan yang ingin menjadi mitra.</p>
    </div>

    @if ($pesan !== '')
        <div class="rounded-lg border text-sm px-4 py-3 {{ $jenisPesan === 'sukses' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-800' }}"
             data-test="pesan-verifikasi">
            {{ $pesan }}
        </div>
    @endif

    {{-- Tab status --}}
    <div class="flex flex-wrap gap-2">
        @foreach (['pending' => 'Menunggu', 'aktif' => 'Aktif', 'ditolak' => 'Ditolak', 'nonaktif' => 'Nonaktif'] as $nilai => $label)
            <button type="button" wire:click="$set('filter', '{{ $nilai }}')"
                class="px-4 py-1.5 rounded-full text-sm border transition cursor-pointer {{ $filter === $nilai ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-white text-gray-600 border-gray-300 hover:border-emerald-400' }}"
                data-test="tab-{{ $nilai }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @php($daftar = $this->daftarMitra())

    <div class="space-y-4">
        @forelse ($daftar as $mitra)
            @php($pemilik = $mitra->users->first())
            <div class="rounded-xl bg-white border border-gray-200 p-5" wire:key="mitra-{{ $mitra->id }}" data-test="kartu-mitra">
                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($mitra->logo)
                        <img src="{{ asset('storage/'.$mitra->logo) }}" alt="Logo {{ $mitra->nama }}"
                             class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                    @endif

                    <div class="flex-1 space-y-1">
                        <h2 class="font-semibold text-gray-900">{{ $mitra->nama }}</h2>
                        <p class="text-sm text-gray-600">{{ $mitra->email }} · {{ $mitra->telepon ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $mitra->alamat ?? '-' }}</p>
                        @if ($mitra->deskripsi)
                            <p class="text-sm text-gray-500">{{ $mitra->deskripsi }}</p>
                        @endif
                        <p class="text-xs text-gray-400">
                            Pemohon: {{ $pemilik?->name ?? '-' }} ({{ $pemilik?->email ?? '-' }})
                            · Diajukan {{ $mitra->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if ($mitra->status === 'ditolak' && $mitra->alasan_penolakan)
                            <p class="text-sm text-red-700">Alasan penolakan: {{ $mitra->alasan_penolakan }}</p>
                        @endif
                    </div>

                    <div class="flex sm:flex-col gap-2 sm:items-end">
                        @if ($mitra->status === 'pending')
                            <button type="button" wire:click="setujui({{ $mitra->id }})"
                                wire:confirm="Setujui mitra &quot;{{ $mitra->nama }}&quot;?"
                                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm px-4 py-2 rounded-lg cursor-pointer"
                                data-test="setujui-button">Setujui</button>
                            <button type="button" wire:click="mulaiTolak({{ $mitra->id }})"
                                class="bg-white border border-red-300 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg cursor-pointer"
                                data-test="tolak-button">Tolak</button>
                        @elseif ($mitra->status === 'aktif')
                            <button type="button" wire:click="nonaktifkan({{ $mitra->id }})"
                                wire:confirm="Nonaktifkan mitra ini? Pemiliknya tidak bisa masuk portal mitra."
                                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg cursor-pointer"
                                data-test="nonaktifkan-button">Nonaktifkan</button>
                        @elseif ($mitra->status === 'nonaktif')
                            <button type="button" wire:click="aktifkan({{ $mitra->id }})"
                                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm px-4 py-2 rounded-lg cursor-pointer"
                                data-test="aktifkan-button">Aktifkan kembali</button>
                        @endif
                    </div>
                </div>

                @if ($tolakId === $mitra->id)
                    <div class="mt-4 border-t border-gray-100 pt-4 space-y-2" data-test="form-tolak">
                        <label for="alasan" class="block text-sm text-gray-600">Alasan penolakan (akan dilihat pemohon)</label>
                        <textarea id="alasan" wire:model="alasan" rows="3"
                            class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 {{ $errors->has('alasan') ? 'border-red-400' : 'border-gray-300' }}"></textarea>
                        @error('alasan')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-2">
                            <button type="button" wire:click="tolak"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg cursor-pointer"
                                data-test="kirim-tolak-button">Kirim penolakan</button>
                            <button type="button" wire:click="batalTolak"
                                class="bg-white border border-gray-300 text-gray-700 text-sm px-4 py-2 rounded-lg cursor-pointer">Batal</button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl bg-white border border-gray-200 p-8 text-center text-sm text-gray-500" data-test="kosong">
                Tidak ada mitra dengan status ini.
            </div>
        @endforelse
    </div>

    {{ $daftar->links() }}
</div>