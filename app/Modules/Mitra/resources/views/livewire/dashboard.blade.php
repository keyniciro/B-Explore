<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::portal'), Title('Dashboard Mitra')] class extends Component {
    public function mitra()
    {
        return Auth::user()->mitra;
    }
}; ?>

<div class="space-y-6">
    @php($mitra = $this->mitra())

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $mitra->nama }}</h1>
        <p class="text-gray-500 mt-1">
            Dashboard mitra
            <span class="ml-1 inline-block text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full align-middle">
                {{ $mitra->status }}
            </span>
        </p>
    </div>

    <a href="{{ route('dashboard') }}" class="inline-block text-sm text-emerald-700 hover:underline">← Kembali ke dashboard wisatawan (belanja &amp; pesanan)</a>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Email</p>
            <p class="font-medium text-gray-900 break-all">{{ $mitra->email }}</p>
        </div>
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Komisi platform</p>
            <p class="font-medium text-gray-900">{{ rtrim(rtrim($mitra->persentase_komisi, '0'), '.') }}%</p>
        </div>
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Kelola tiket &amp; paket</p>
            <p class="font-medium text-gray-900">Segera hadir</p>
        </div>
    </div>
</div>
