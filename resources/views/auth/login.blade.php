<x-guest-layout>
    <div class="mb-5">
        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Sign In</p>
        <h2 class="mt-1 text-2xl font-bold text-[#0B4D2C]">Welcome back</h2>
        <p class="mt-1 text-sm text-slate-600">Enter your credentials to access your account.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-slate-700" />
            <x-text-input id="email" class="mt-1 block w-full rounded-lg border-slate-300 px-3 py-2.5 text-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-slate-700" />

            <div class="relative">
                <x-text-input id="password" class="mt-1 block w-full rounded-lg border-slate-300 px-3 py-2.5 text-sm pr-10"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="Enter your password" />
                <button type="button" aria-label="Show password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700" onclick="togglePassword('password', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#0B4D2C] shadow-sm focus:ring-[#32CD32]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 underline underline-offset-4 hover:text-slate-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-[#0B4D2C] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#083b22] sm:w-auto">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>
