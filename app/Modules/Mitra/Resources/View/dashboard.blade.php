<x-layouts::app title="Dashboard Mitra">
    <flux:heading size="xl">{{ auth()->user()->mitra->nama }}</flux:heading>
    <p class="text-sm text-zinc-500">Portal mitra loket wisata.</p>
</x-layouts::app>