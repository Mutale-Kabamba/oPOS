<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Accountant Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Settings</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.accounts.index') }}" class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Bookkeeping</p>
                <p class="mt-1.5 text-sm font-bold text-[#0B4D2C]">Chart of Accounts</p>
                <p class="mt-1 text-xs text-slate-600">Manage account codes and structures.</p>
            </a>

            <a href="{{ route('profile.edit') }}" class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Account</p>
                <p class="mt-1.5 text-sm font-bold text-[#0B4D2C]">Profile</p>
                <p class="mt-1 text-xs text-slate-600">Update your details and password.</p>
            </a>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-4">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Posting Rules</p>
                <h3 class="mt-1 text-sm font-bold text-[#0B4D2C]">Journal Settlement and Contra Accounts</h3>
                <p class="mt-1 text-xs text-slate-600">These accounts are used when the system auto-posts balanced journal entries from transactions.</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.posting-rules') }}" class="grid gap-4 md:grid-cols-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Settlement Account</label>
                    <select name="settlement_account_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        @foreach ($postingRuleAccounts as $account)
                            <option value="{{ $account->id }}" @selected((int) old('settlement_account_id', $postingRules['settlement_account_id']) === (int) $account->id)>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('settlement_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Contra Account</label>
                    <select name="contra_account_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        @foreach ($postingRuleAccounts as $account)
                            <option value="{{ $account->id }}" @selected((int) old('contra_account_id', $postingRules['contra_account_id']) === (int) $account->id)>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('contra_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Save Posting Rules</button>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2.5 sm:px-4">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Recent User Activity</h3>
                <a href="{{ route('admin.settings.activity.pdf') }}" class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50">Export PDF</a>
            </div>

            @if ($activity->isEmpty())
                <div class="px-5 py-10 text-center">
                    <div class="mx-auto mb-2.5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">No activity yet</p>
                    <p class="mt-1 text-xs text-slate-500">System logs will appear as users start working.</p>
                </div>
            @else
                <div x-data="{ showAll: false }">
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">When</th>
                                <th class="px-3 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                                <th class="px-3 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activity as $log)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70" x-show="showAll || {{ $loop->index }} < 5">
                                    <td class="px-3 py-2.5 font-medium text-slate-700">{{ $log->occurred_at?->format('d M Y') }}</td>
                                    <td class="cell-truncate px-3 py-2.5 font-medium text-slate-700">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="cell-truncate px-3 py-2.5 text-slate-700">{{ $log->description ?: str_replace('_', ' ', ucfirst($log->action)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-recent-table-footer :total="$activity->count()" />
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
