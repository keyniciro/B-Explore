@props(['title' => null])
@php
    $user = auth()->user();
    $adalahMitra = $user?->hasRole(\App\Models\User::ROLE_MITRA) ?? false;
    // Asal logout menentukan tujuan setelahnya: dari halaman kelola mitra -> /mitra/login, lainnya -> beranda.
    $portal = request()->is('mitra/*') && ! request()->routeIs('mitra.daftar') ? 'mitra' : 'wisatawan';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ filled($title) ? $title.' - ' : '' }}B-Explore</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans min-h-screen text-gray-800">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
            <a href="{{ $user?->homeUrl() ?? route('home') }}" class="text-xl italic font-bold text-emerald-700">B-Explore</a>

            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-emerald-700">Dashboard</a>

                @if ($adalahMitra)
                    <a href="{{ route('mitra.dashboard') }}" class="text-sm font-medium text-emerald-700 hover:underline" data-test="kelola-mitra-link">Kelola Mitra</a>
                @endif

                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-emerald-700">Profil</a>

                <span class="text-sm text-gray-600">
                    {{ $user?->name }}
                    <span class="ml-1 inline-block text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full align-middle">
                        {{ $user?->role }}
                    </span>
                </span>

                {{-- Logout lewat endpoint Fortify (logout + invalidate sesi + regenerasi token CSRF) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <input type="hidden" name="portal" value="{{ $portal }}">
                    <button type="submit" class="text-sm font-medium text-red-600 hover:underline" data-test="logout-button">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-10">
        <x-explore.alert />

        {{ $slot }}
    </main>
</body>
</html>
