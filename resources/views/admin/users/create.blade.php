<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Create User</h2>
            </div>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" class="w-full rounded-lg border-slate-300 text-sm" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" class="w-full rounded-lg border-slate-300 text-sm" name="password" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                    <select class="w-full rounded-lg border-slate-300 text-sm" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="accountant" selected>Accountant</option>
                        <option value="salesperson">Salesperson (POS)</option>
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-[#32CD32] focus:ring-[#32CD32]">
                    Active account
                </label>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Save User</button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
