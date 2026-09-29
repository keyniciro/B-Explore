@extends('layouts.admin')
@section('judul', 'Kelola Tiket')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Kelola Jenis Tiket</h1>
        <a href="{{ route('admin.jenis-tiket.create') }}" class="rounded bg-blue-600 text-white px-4 py-2 text-sm">+ Tambah Tiket</a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama tiket..." class="rounded border border-gray-300 px-3 py-2 flex-1">
        <select name="status" class="rounded border border-gray-300 px-3 py-2">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
        <button class="rounded border px-4 py-2 bg-white">Filter</button>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3">Nama tiket</th>
                    <th class="px-4 py-3">Destinasi</th>
                    @if(! auth()->user()->tenant_id)<th class="px-4 py-3">Mitra</th>@endif
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Kuota/hari</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tiket as $t)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $t->nama }}</td>
                        <td class="px-4 py-3">{{ $t->destinasi?->nama }}</td>
                        @if(! auth()->user()->tenant_id)<td class="px-4 py-3">{{ $t->mitra?->nama }}</td>@endif
                        <td class="px-4 py-3">{{ $t->harga_rupiah }}</td>
                        <td class="px-4 py-3">{{ $t->kuota_harian ?? '∞' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded px-2 py-0.5 text-xs {{ $t->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">{{ ucfirst($t->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.jenis-tiket.edit', $t) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.jenis-tiket.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus tiket ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline ml-3">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada tiket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tiket->links() }}</div>
@endsection
