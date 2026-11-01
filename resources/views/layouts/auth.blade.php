@props(['title' => null])
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
<body class="font-serif">
    <div class="relative min-h-screen bg-cover bg-center flex items-center justify-center md:justify-end px-6 md:px-20"
         style="background-image: url('{{ asset('images/bg.jpg') }}')">

        {{-- tombol kembali ke halaman awal --}}
        <a href="{{ route('home') }}"
           class="absolute top-6 left-6 z-10 w-10 h-10 rounded-full border-2 border-emerald-600 bg-white/70 flex items-center justify-center text-emerald-700 hover:bg-white transition"
           aria-label="Kembali ke beranda">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
            </svg>
        </a>

        {{-- caption kiri bawah --}}
        <div class="hidden md:block absolute bottom-16 left-16 z-10 bg-white/40 backdrop-blur-sm rounded-lg px-6 py-4 max-w-sm">
            <p class="italic text-lg font-bold text-gray-900 leading-tight">Welcome to Banyuwangi</p>
            <p class="italic text-lg font-bold text-emerald-700 leading-tight">The Sunrise of Java</p>
        </div>

        {{-- kartu form --}}
        <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl p-10 my-10">
            <h1 class="text-3xl italic font-bold text-center text-gray-900 mb-8">B-Explore</h1>

            <x-explore.alert />

            {{ $slot }}
        </div>
    </div>

    @fluxScripts
</body>
</html>
