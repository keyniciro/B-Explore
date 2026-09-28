<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'B-Explore')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-serif">
    <div class="relative min-h-screen bg-cover bg-center flex items-center justify-end px-6 md:px-20"
        style="background-image: url('{{ asset('images/bg.jpg') }}')">

        {{-- tombol kembali ke welcome page --}}
        <a href="{{ route('welcome') }}"
        class="absolute top-6 left-6 z-10 w-10 h-10 rounded-full border-2 border-emerald-600 bg-white/70 flex items-center justify-center text-emerald-700 hover:bg-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
            </svg>
        </a>

        {{-- caption kiri bawah --}}
        <div class="absolute bottom-16 left-6 md:left-16 z-10 bg-white/40 backdrop-blur-sm rounded-lg px-6 py-4 max-w-sm">
            <p class="italic text-lg font-bold text-gray-900 leading-tight">Welcome to Banyuwangi</p>
            <p class="italic text-lg font-bold text-emerald-700 leading-tight">The Sunrise of Java</p>
        </div>

        {{-- card form --}}
        <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl p-10 my-10">
            <h1 class="text-3xl italic font-bold text-center text-gray-900 mb-8">B-Explore</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
