<x-layouts::auth title="Login Admin">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="portal" value="admin">

        <p class="text-center text-sm text-gray-500">Khusus Super Admin</p>

        <x-explore.field label="Email" name="email" type="email" :value="old('email')"
            required autofocus autocomplete="email" />

        <x-explore.field label="Password" name="password" type="password"
            required autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            Ingat saya
        </label>

        <x-explore.button data-test="login-button">Login Admin</x-explore.button>
    </form>
</x-layouts::auth>