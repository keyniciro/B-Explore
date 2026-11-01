<x-layouts::auth title="Login Mitra">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="portal" value="mitra">

        <x-explore.field label="Email" name="email" type="email" :value="old('email')"
            required autofocus autocomplete="email" />

        <x-explore.field label="Password" name="password" type="password"
            required autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            Ingat saya
        </label>

        <x-explore.button data-test="login-button">Login Mitra</x-explore.button>

        <p class="text-center text-xs text-gray-400">
            Belum jadi mitra? Daftar sebagai wisatawan terlebih dahulu, lalu pilih
            <span class="font-medium">Daftar Mitra</span> dari dashboard.
        </p>
        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-emerald-600 hover:underline">← Login wisatawan</a>
        </p>
    </form>
</x-layouts::auth>
