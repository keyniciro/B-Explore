@extends('layouts.guest')

@section('title', 'Login Mitra')

@section('content')
    <form method="POST" action="{{ route('mitra.login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <button type="submit"
                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg transition">
            Login Mitra
        </button>

        <p class="text-center text-xs text-gray-400">
            Belum jadi mitra? Daftar sebagai wisatawan terlebih dahulu, lalu ajukan pendaftaran mitra dari dashboard.
        </p>
    </form>
@endsection
