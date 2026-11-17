<?php

use App\Models\Mitra;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::portal'), Title('Dashboard Admin')] class extends Component {
    /** @return array<string, int> */
    public function ringkasan(): array
    {
        $perStatus = Mitra::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'pending' => (int) ($perStatus[Mitra::STATUS_PENDING] ?? 0),
            'aktif' => (int) ($perStatus[Mitra::STATUS_AKTIF] ?? 0),
            'ditolak' => (int) ($perStatus[Mitra::STATUS_DITOLAK] ?? 0),
            'wisatawan' => User::query()->where('role', User::ROLE_WISATAWAN)->count(),
        ];
    }
}; ?>

<div class="space-y-6">
    @php($r = $this->ringkasan())

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Super Admin</h1>
        <p class="text-gray-500 mt-1">Ringkasan pendaftaran mitra B-Explore.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.verifikasi-mitra', ['status' => 'pending']) }}"
           class="rounded-xl bg-amber-50 border border-amber-200 p-5 hover:border-amber-400 transition" data-test="kartu-pending">
            <p class="text-sm text-amber-800">Menunggu verifikasi</p>
            <p class="text-3xl font-bold text-amber-900">{{ $r['pending'] }}</p>
        </a>
        <a href="{{ route('admin.verifikasi-mitra', ['status' => 'aktif']) }}"
           class="rounded-xl bg-white border border-gray-200 p-5 hover:border-emerald-400 transition">
            <p class="text-sm text-gray-500">Mitra aktif</p>
            <p class="text-3xl font-bold text-gray-900">{{ $r['aktif'] }}</p>
        </a>
        <a href="{{ route('admin.verifikasi-mitra', ['status' => 'ditolak']) }}"
           class="rounded-xl bg-white border border-gray-200 p-5 hover:border-emerald-400 transition">
            <p class="text-sm text-gray-500">Pengajuan ditolak</p>
            <p class="text-3xl font-bold text-gray-900">{{ $r['ditolak'] }}</p>
        </a>
        <div class="rounded-xl bg-white border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Wisatawan</p>
            <p class="text-3xl font-bold text-gray-900">{{ $r['wisatawan'] }}</p>
        </div>
    </div>
</div>
