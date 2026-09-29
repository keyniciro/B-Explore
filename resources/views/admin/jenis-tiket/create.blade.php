@extends('layouts.admin')
@section('judul', 'Tambah Tiket')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Tambah Jenis Tiket</h1>

    <form method="POST" action="{{ route('admin.jenis-tiket.store') }}" class="bg-white rounded shadow p-6">
        @csrf
        @include('admin.jenis-tiket._form')

        <div class="mt-6 flex gap-3">
            <button class="rounded bg-blue-600 text-white px-4 py-2">Simpan</button>
            <a href="{{ route('admin.jenis-tiket.index') }}" class="rounded border px-4 py-2">Batal</a>
        </div>
    </form>
@endsection
