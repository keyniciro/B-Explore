<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>B-Explore — Banyuwangi</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="relative min-h-screen bg-cover bg-center flex items-center justify-center"
         style="background-image: url('{{ asset('images/bg.jpg') }}')">
        <div class="absolute inset-0 bg-black/30"></div>

        <div class="relative z-10 text-center text-white px-6">
            <h1 class="text-4xl md:text-5xl italic font-bold mb-2">Welcome to Banyuwangi</h1>
            <p class="text-xl md:text-2xl italic text-emerald-300 mb-10">The Sunrise of Java</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                   class="bg-emerald-700 hover:bg-emerald-800 px-6 py-3 rounded-lg font-medium transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="bg-white/90 hover:bg-white text-emerald-700 px-6 py-3 rounded-lg font-medium transition">
                    Daftar
                </a>
            </div>

            <p class="mt-8 text-sm text-white/80">
                Pengelola wisata?
                <a href="{{ route('mitra.login') }}" class="underline hover:text-white">Masuk sebagai mitra</a>
            </p>
        </div>
    </div>
</body>
</html>
