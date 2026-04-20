<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.settings') : route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">
                {{ auth()->user()->isAdmin() ? 'Back to Settings' : 'Back to Dashboard' }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="border-t border-slate-200"></div>

                <div class="p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="border-t border-slate-200"></div>

                <div class="p-4 sm:p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
