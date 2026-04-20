<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Accounting Rules</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Create Category</h2>
            </div>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input class="w-full rounded-lg border-slate-300 text-sm" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Type</label>
                    <select class="w-full rounded-lg border-slate-300 text-sm" name="type" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea rows="4" class="w-full rounded-lg border-slate-300 text-sm" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Save Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
