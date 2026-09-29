@php
    $input = 'w-full rounded border border-gray-300 px-3 py-2';
    $err = fn ($f) => $errors->first($f) ? '<p class="text-sm text-red-600 mt-1">' . e($errors->first($f)) . '</p>' : '';
@endphp

<div class="space-y-4">
    @if($daftarMitra->isNotEmpty())
        <div>
            <label class="block text-sm font-medium mb-1">Mitra</label>
            <select name="tenant_id" class="{{ $input }}">
                <option value="">-- Pilih mitra --</option>
                @foreach($daftarMitra as $m)
                    <option value="{{ $m->id }}" @selected(old('tenant_id', $tiket->tenant_id) == $m->id)>{{ $m->nama }}</option>
                @endforeach
            </select>
            {!! $err('tenant_id') !!}
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium mb-1">Destinasi</label>
        <select name="destinasi_id" class="{{ $input }}">
            <option value="">-- Pilih destinasi --</option>
            @foreach($daftarDestinasi as $d)
                <option value="{{ $d->id }}" @selected(old('destinasi_id', $tiket->destinasi_id) == $d->id)>{{ $d->nama }}</option>
            @endforeach
        </select>
        {!! $err('destinasi_id') !!}
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nama tiket</label>
        <input type="text" name="nama" value="{{ old('nama', $tiket->nama) }}" class="{{ $input }}" placeholder="Contoh: Tiket Wisatawan Domestik">
        {!! $err('nama') !!}
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="{{ $input }}">{{ old('deskripsi', $tiket->deskripsi) }}</textarea>
        {!! $err('deskripsi') !!}
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
            <input type="number" name="harga" min="0" step="1" value="{{ old('harga', $tiket->harga) }}" class="{{ $input }}">
            {!! $err('harga') !!}
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kuota harian <span class="text-gray-400">(kosong = tanpa batas)</span></label>
            <input type="number" name="kuota_harian" min="1" value="{{ old('kuota_harian', $tiket->kuota_harian) }}" class="{{ $input }}">
            {!! $err('kuota_harian') !!}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Berlaku dari</label>
            <input type="date" name="berlaku_dari" value="{{ old('berlaku_dari', optional($tiket->berlaku_dari)->format('Y-m-d')) }}" class="{{ $input }}">
            {!! $err('berlaku_dari') !!}
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Berlaku sampai</label>
            <input type="date" name="berlaku_sampai" value="{{ old('berlaku_sampai', optional($tiket->berlaku_sampai)->format('Y-m-d')) }}" class="{{ $input }}">
            {!! $err('berlaku_sampai') !!}
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="{{ $input }}">
            <option value="aktif" @selected(old('status', $tiket->status) === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(old('status', $tiket->status) === 'nonaktif')>Nonaktif</option>
        </select>
        {!! $err('status') !!}
    </div>
</div>
