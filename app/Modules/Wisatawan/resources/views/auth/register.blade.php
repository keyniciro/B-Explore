<x-layouts::auth title="Daftar Akun">
    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
        @csrf

        <x-explore.field label="Nama" name="name" :value="old('name')"
            required autofocus autocomplete="name" />

        <x-explore.field label="Email" name="email" type="email" :value="old('email')"
            required autocomplete="email" />

        <x-explore.field label="Password" name="password" type="password"
            required autocomplete="new-password" />

        <x-explore.field label="Konfirmasi Password" name="password_confirmation" type="password"
            required autocomplete="new-password" />

        <x-explore.button data-test="register-user-button">Buat Akun</x-explore.button>

        <p class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-emerald-600 font-medium hover:underline">Login</a>
        </p>
    </form>
</x-layouts::auth>
