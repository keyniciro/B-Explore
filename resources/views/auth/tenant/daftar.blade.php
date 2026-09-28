@extends('layouts.app')

@section('title', 'Daftar Mitra')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-10">
        <h1 class="text-2xl italic font-bold text-center text-gray-900 mb-1">Daftar Jadi Mitra</h1>
        <p class="text-center text-sm text-gray-500 mb-8">
            Isi data usaha Anda. Setelah berhasil, akun Anda otomatis mendapat akses dashboard mitra.
        </p>

        <form method="POST" action="{{ route('mitra.daftar') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">Nama Mitra / Usaha</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required autofocus
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-400 mt-1">Slug akan dibuat otomatis dari nama ini.</p>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Email Mitra</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Alamat</label>
                <textarea name="alamat" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('alamat') }}</textarea>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Logo (opsional)</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-600">
            </div>

            <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium py-2.5 rounded-lg transition">
                Daftar Mitra
            </button>
        </form>
    </div>
@endsection
