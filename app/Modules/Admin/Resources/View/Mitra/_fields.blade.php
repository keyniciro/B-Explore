?php
@php($m = $mitra ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <flux:input name="nama" label="Nama mitra" :value="old('nama', $m?->nama)" required />
    <flux:input name="email" type="email" label="Email usaha" :value="old('email', $m?->email)" required />
    <flux:input name="telepon" label="Telepon" :value="old('telepon', $m?->telepon)" />
    <flux:input name="persentase_komisi" type="number" step="0.01" min="0" max="100"
                label="Komisi platform (%)" :value="old('persentase_komisi', $m?->persentase_komisi ?? 10)" required />
</div>

<flux:input name="alamat" label="Alamat" :value="old('alamat', $m?->alamat)" />

<div>
    <label for="deskripsi" class="mb-1 block text-sm font-medium">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" rows="3"
              class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">{{ old('deskripsi', $m?->deskripsi) }}</textarea>
    @error('deskripsi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>