<section>
    <header>
        <h2 class="text-base font-semibold text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-xs text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
            <div class="mt-1.5 flex items-center gap-3">
                <span class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0B4D2C&color=ffffff&size=128';">
                </span>
                <x-text-input id="profile_photo" name="profile_photo" type="file" class="block w-full" accept="image/png,image/jpeg,image/webp" />
            </div>
            <p class="mt-1 text-xs text-gray-500">PNG, JPG, or WEBP. Max 2MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-xs mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
