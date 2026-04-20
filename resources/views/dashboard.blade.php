<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Overview</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Dashboard</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
                <a href="{{ route('accounting.transactions.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">
                    <span class="sm:hidden">+ Entry</span>
                    <span class="hidden sm:inline">+ Record Entry</span>
                </a>
                <a href="{{ route('reports.transactions.pdf') }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">
                    <span class="sm:hidden">Export</span>
                    <span class="hidden sm:inline">Export PDF</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $rows = $rows ?? collect();
        $income = $income ?? 0;
        $expense = $expense ?? 0;
        $netProfit = $netProfit ?? ($income - $expense);
    @endphp

    <div class="space-y-6">
        <section class="stat-grid sm:grid-cols-2 xl:grid-cols-3">
            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">Total Income</p>
                    <span class="stat-icon bg-green-100 text-green-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m5-7H7"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ 'K ' . number_format($income, 2) }}</p>
            </article>

            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">Total Expense</p>
                    <span class="stat-icon bg-orange-100 text-orange-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12h10"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ 'K ' . number_format($expense, 2) }}</p>
            </article>

            <article class="stat-card stat-card-wide">
                <div class="stat-head">
                    <p class="stat-title">Net Profit</p>
                    <span class="stat-icon {{ $netProfit >= 0 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l6-6 4 4 6-6"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ 'K ' . number_format($netProfit, 2) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Recent Transactions</h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.transactions.pdf', ['from' => '2000-01-01', 'to' => now()->toDateString(), 'report_type' => 'full']) }}" class="rounded-lg border border-[#FF6B35] px-3 py-1.5 text-xs font-medium text-[#FF6B35] hover:bg-orange-50">Export PDF</a>
                    <a href="{{ route('reports.transactions') }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">View All</a>
                </div>
            </div>

            @if ($rows->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h8"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No transactions yet</p>
                    <p class="mt-1 text-sm text-slate-500">Record your first entry to start generating insights.</p>
                </div>
            @else
                <div x-data="{ showAll: false }">
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70" x-show="showAll || {{ $loop->index }} < 5">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $row->date?->format('d M Y') }}</td>
                                    <td class="cell-truncate px-4 py-3 font-medium text-slate-700">{{ $row->category?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if (($row->category?->type ?? '') === 'income')
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Income</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">Expense</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ 'K ' . number_format($row->displayAmount(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-recent-table-footer :total="$rows->count()" />
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
