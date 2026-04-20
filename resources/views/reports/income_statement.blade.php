<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Income Statement</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">
                    <span class="sm:hidden">Back</span>
                    <span class="hidden sm:inline">Back to Reports</span>
                </a>
                <a href="{{ route('reports.income-statement.pdf', ['from' => $from, 'to' => $to]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">
                    <span class="sm:hidden">PDF</span>
                    <span class="hidden sm:inline">Monthly PDF</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">
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
                <button class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]" type="submit">
                    <span class="sm:hidden">Apply</span>
                    <span class="hidden sm:inline">Apply Filter</span>
                </button>
            </form>
        </section>

        <section class="stat-grid sm:grid-cols-3">
            <article class="stat-card">
                <p class="stat-title">Total Income</p>
                <p class="stat-value">{{ 'K ' . number_format($totalIncome, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Direct Costs (COGS)</p>
                <p class="stat-value">{{ 'K ' . number_format($directCosts, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Gross Profit</p>
                <p class="stat-value {{ $grossProfit >= 0 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($grossProfit, 2) }}</p>
            </article>
        </section>

        <section class="stat-grid sm:grid-cols-2">
            <article class="stat-card">
                <p class="stat-title">General Expenses</p>
                <p class="stat-value">{{ 'K ' . number_format($generalExpenses, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Net Profit</p>
                <p class="stat-value {{ $netProfit >= 0 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($netProfit, 2) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 px-4 py-3 text-sm font-medium text-slate-600">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
            <div class="table-shell">
                <table class="table-compact min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Month</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Income</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">COGS</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Gross</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Expenses</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($monthlyRows as $row)
                            <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row['month'] }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ 'K ' . number_format($row['income'], 2) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ 'K ' . number_format($row['cogs'], 2) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ 'K ' . number_format($row['gross'], 2) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ 'K ' . number_format($row['expenses'], 2) }}</td>
                                <td class="px-4 py-3 font-medium {{ $row['net'] >= 0 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($row['net'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No data found for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
