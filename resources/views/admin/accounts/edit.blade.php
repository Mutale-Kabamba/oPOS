<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Bookkeeping Setup</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Edit Account</h2>
            </div>
            <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 sm:w-auto">Back to Settings</a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.accounts.update', $account) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Code</label>
                    <input name="code" value="{{ old('code', $account->code) }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                    @error('code')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input name="name" value="{{ old('name', $account->name) }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Type</label>
                        <select name="type" class="w-full rounded-lg border-slate-300 text-sm" required>
                            @foreach (['asset' => 'Asset', 'liability' => 'Liability', 'income' => 'Income', 'cogs' => 'COGS', 'expense' => 'Expense'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $account->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Group</label>
                        <select name="group_name" class="w-full rounded-lg border-slate-300 text-sm" required>
                            @foreach (['valuables' => 'Valuables', 'debts' => 'Debts', 'money_in' => 'Money In', 'direct_costs' => 'Direct Costs', 'general_costs' => 'General Costs'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('group_name', $account->group_name) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('group_name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active)) class="rounded border-slate-300 text-[#32CD32] focus:ring-[#32CD32]">
                    Active account
                </label>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Update Account</button>
                    <a href="{{ route('admin.accounts.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
