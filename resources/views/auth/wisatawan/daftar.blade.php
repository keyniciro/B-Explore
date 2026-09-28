@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <form method="POST" action="{{ route('wisatawan.daftar') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm text-gray-600 mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <button type="submit"
                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg transition">
            Create Account
        </button>

        <p class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('wisatawan.login') }}" class="text-emerald-600 font-medium hover:underline">Login</a>
        </p>
    </form>
@endsection
