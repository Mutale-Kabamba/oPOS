<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Reconciliation Report</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Reports</a>
                <a href="{{ route('reports.reconciliation.pdf', ['account_id' => $selectedAccount?->id, 'as_of' => $asOf, 'statement_ending_balance' => $statementEndingBalance]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">PDF Export</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="grid gap-4 md:grid-cols-4">
                <div>
                    <label class="block text-sm text-slate-600">Asset Account</label>
                    <select name="account_id" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" @selected(optional($selectedAccount)->id === $account->id)>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600">As Of</label>
                    <input type="date" name="as_of" value="{{ $asOf }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-slate-600">Ending Bank Statement Balance</label>
                    <input type="number" step="0.01" name="statement_ending_balance" value="{{ $statementEndingBalance }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm" placeholder="0.00">
                </div>
                <div class="flex items-end">
                    <button class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]" type="submit">Load Reconciliation</button>
                </div>
            </form>
        </section>

        @if (! $selectedAccount)
            <section class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm">
                No active asset accounts are available for reconciliation.
            </section>
        @else
            <section class="stat-grid sm:grid-cols-2 xl:grid-cols-4">
                <article class="stat-card">
                    <p class="stat-title">Selected Account</p>
                    <p class="stat-value-sm">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</p>
                </article>
                <article class="stat-card">
                    <p class="stat-title">System Cleared Balance</p>
                    <p class="stat-value">{{ 'K ' . number_format($clearedBalance, 2) }}</p>
                </article>
                <article class="stat-card">
                    <p class="stat-title">Outstanding Uncleared</p>
                    <p class="stat-value">{{ 'K ' . number_format($unclearedBalance, 2) }}</p>
                </article>
                <article class="stat-card">
                    <p class="stat-title">Variance</p>
                    <p class="stat-value {{ $variance === null || abs($variance) < 0.01 ? 'text-green-700' : 'text-orange-700' }}">
                        {{ $variance === null ? '—' : 'K ' . number_format($variance, 2) }}
                    </p>
                </article>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-sm text-slate-600">
                    Select uncleared transactions that appear on the bank statement, then mark them as reconciled.
                </div>

                <form method="POST" action="{{ route('reports.reconciliation.reconcile') }}">
                    @csrf
                    <input type="hidden" name="account_id" value="{{ $selectedAccount->id }}">
                    <input type="hidden" name="as_of" value="{{ $asOf }}">
                    <input type="hidden" name="statement_ending_balance" value="{{ $statementEndingBalance }}">

                    <div class="table-shell">
                        <table class="table-compact min-w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Clear</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Supplier</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Movement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($unclearedRows as $row)
                                    <tr class="odd:bg-white even:bg-[#F4F7F5]">
                                        <td class="px-4 py-3 align-top"><input type="checkbox" name="transaction_ids[]" value="{{ $row->id }}" class="rounded border-slate-300"></td>
                                        <td class="px-4 py-3">{{ $row->date->format('d M Y') }}</td>
                                        <td class="px-4 py-3">{{ $row->description ?: '—' }}</td>
                                        <td class="px-4 py-3">{{ $row->supplier?->name ?: '—' }}</td>
                                        <td class="px-4 py-3">{{ $row->user?->name ?: '—' }}</td>
                                        <td class="px-4 py-3 text-right {{ $row->movement_amount >= 0 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($row->movement_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No uncleared transactions found for this account and date.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200 px-4 py-3">
                        <p class="text-sm text-slate-500">Only selected uncleared transactions will be updated.</p>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white" @disabled($unclearedRows->isEmpty())>Mark Selected as Reconciled</button>
                    </div>
                </form>
            </section>
        @endif
    </div>
</x-app-layout>