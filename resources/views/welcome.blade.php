<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Explore — Banyuwangi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="relative min-h-screen bg-cover bg-center flex items-center justify-center"
        style="background-image: url('{{ asset('images/bg.jpg') }}')">
        <div class="absolute inset-0 bg-black/30"></div>

        <div class="relative z-10 text-center text-white px-6">
            <h1 class="text-4xl md:text-5xl italic font-bold mb-2">Welcome to Banyuwangi</h1>
            <p class="text-xl md:text-2xl italic text-emerald-300 mb-10">The Sunrise of Java</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('wisatawan.login') }}"
                class="bg-emerald-700 hover:bg-emerald-800 px-6 py-3 rounded-lg font-medium transition">
                    Login
                </a>
                <a href="{{ route('wisatawan.daftar') }}"
                class="bg-white/90 hover:bg-white text-emerald-700 px-6 py-3 rounded-lg font-medium transition">
                    Daftar
                </a>
            </div>
        </div>
    </div>
</body>
</html>