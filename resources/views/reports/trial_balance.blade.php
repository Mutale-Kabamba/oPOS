<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Trial Balance</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Reports</a>
                <a href="{{ route('reports.trial-balance.pdf', ['from' => $from, 'to' => $to]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">PDF Export</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl space-y-4">
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm text-slate-600">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-slate-600">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <button class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]" type="submit">Apply Filter</button>
            </form>
        </section>

        <section class="stat-grid sm:grid-cols-3">
            <article class="stat-card">
                <p class="stat-title">Total Debits</p>
                <p class="stat-value">{{ 'K ' . number_format($totalDebits, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Total Credits</p>
                <p class="stat-value">{{ 'K ' . number_format($totalCredits, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Difference</p>
                <p class="stat-value {{ abs($difference) < 0.01 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($difference, 2) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-sm">
            Closing balances are mapped by natural account side. Assets, COGS, and expenses display on the debit side. Liabilities and income display on the credit side.
            @if (abs($difference) >= 0.01)
                <span class="font-medium text-orange-700"> The current dataset is out of balance by {{ 'K ' . number_format($difference, 2) }}.</span>
            @endif
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="table-shell">
                <table class="table-compact min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Account</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Debit</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Credit</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->code }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ strtoupper($row->type) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">{{ $row->debit > 0 ? 'K ' . number_format($row->debit, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">{{ $row->credit > 0 ? 'K ' . number_format($row->credit, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-800">{{ 'K ' . number_format($row->closing_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No accounts found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-bold uppercase tracking-wider text-slate-600">Totals</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-slate-800">{{ 'K ' . number_format($totalDebits, 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-slate-800">{{ 'K ' . number_format($totalCredits, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>