<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Admin') - Wisata Banyuwangi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-white border-b px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <span class="font-semibold">Panel Admin</span>
            <a href="{{ route('admin.jenis-tiket.index') }}" class="text-sm hover:underline">Kelola Tiket</a>
        </div>
        <span class="text-sm text-gray-500">
            {{ auth()->user()->name }}
            @if(auth()->user()->tenant_id) · {{ auth()->user()->mitra?->nama }} @else · Admin Platform @endif
        </span>
    </nav>

    <main class="max-w-5xl mx-auto p-6">
        @if(session('sukses'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">{{ session('sukses') }}</div>
        @endif
        @if(session('gagal'))
            <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2">{{ session('gagal') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
