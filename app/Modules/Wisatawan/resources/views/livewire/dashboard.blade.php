<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::portal'), Title('Dashboard')] class extends Component {
    public function name(): string
    {
        return Auth::user()->name;
    }
}; ?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Halo, {{ $this->name() }} 👋</h1>
        <p class="text-gray-500 mt-1">Selamat datang di B-Explore. Temukan destinasi dan tiket wisata Banyuwangi.</p>
    </div>

    {{-- Placeholder fitur modul Wisatawan. Anggota tim lain mengganti kartu ini dengan komponen Livewire fiturnya. --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="font-semibold text-gray-900">Cari Destinasi</p>
            <p class="text-sm text-gray-500 mt-1">Segera hadir.</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="rounded-xl bg-white border border-gray-200 p-5 hover:border-emerald-400 transition">
            <p class="font-semibold text-gray-900">Profil Saya</p>
            <p class="text-sm text-gray-500 mt-1">Ubah nama, email, dan keamanan akun.</p>
        </a>
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="font-semibold text-gray-900">Pesanan &amp; Pembayaran</p>
            <p class="text-sm text-gray-500 mt-1">Segera hadir.</p>
        </div>
    </div>

    {{-- Pintu ke portal mitra. Mitra = wisatawan + hak kelola, jadi dashboard ini tetap miliknya. --}}
    @if (auth()->user()->hasRole(\App\Models\User::ROLE_MITRA))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-6">
            <h2 class="font-semibold text-emerald-900">{{ auth()->user()->mitra->nama }}</h2>
            <p class="text-sm text-emerald-800/80 mt-1">
                Anda juga terdaftar sebagai mitra. Kelola tiket dan destinasi Anda di portal mitra.
            </p>
            <a href="{{ route('mitra.dashboard') }}"
               class="mt-4 inline-block bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition"
               data-test="kelola-mitra-button">
                Kelola Mitra
            </a>
        </div>
    @elseif (auth()->user()->role === \App\Models\User::ROLE_MITRA)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-6 text-sm text-amber-900" data-test="mitra-nonaktif-notice">
            Mitra Anda sedang dinonaktifkan. Anda tetap bisa memakai akun ini sebagai wisatawan; hubungi admin untuk mengaktifkannya kembali.
        </div>
    @else
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-6">
            <h2 class="font-semibold text-emerald-900">Punya usaha wisata?</h2>
            <p class="text-sm text-emerald-800/80 mt-1">
                Daftarkan usaha Anda sebagai mitra untuk menjual tiket dan paket wisata. Akun ini tetap bisa dipakai belanja seperti biasa.
            </p>

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('mitra.daftar') }}"
                   class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2.5 rounded-lg text-center transition"
                   data-test="daftar-mitra-link">
                    Daftar Mitra
                </a>

                {{-- Punya akun mitra LAIN? Keluar dulu dari sesi ini, lalu ke /mitra/login. --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <input type="hidden" name="portal" value="mitra">
                    <button type="submit"
                        class="w-full sm:w-auto bg-white hover:bg-emerald-100 text-emerald-700 border border-emerald-300 text-sm font-medium px-5 py-2.5 rounded-lg transition cursor-pointer"
                        data-test="login-mitra-button">
                        Login Mitra
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
