<x-layouts::auth title="Login">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="portal" value="wisatawan">

        <x-explore.field label="Email" name="email" type="email" :value="old('email')"
            required autofocus autocomplete="email" />

        <x-explore.field label="Password" name="password" type="password"
            required autocomplete="current-password" />

        <div class="flex items-center justify-between text-sm text-gray-600">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                Ingat saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-emerald-600 hover:underline">Lupa password?</a>
            @endif
        </div>

        <p class="text-xs text-gray-400" data-test="lockout-policy">
            5× gagal dalam 10 menit → akun terkunci 15 menit. Sesi berakhir setelah 8 jam tidak aktif.
        </p>

        <x-explore.button data-test="login-button">Login</x-explore.button>

        <p class="text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-emerald-600 font-medium hover:underline">Buat akun</a>
        </p>
        <p class="text-center text-xs text-gray-400">
            Pengelola wisata?
            <a href="{{ route('mitra.login') }}" class="text-emerald-600 hover:underline">Login sebagai mitra</a>
        </p>
    </form>
</x-layouts::auth>
