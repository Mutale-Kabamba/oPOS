<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Sales Reports</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Reports</a>
                <a href="{{ route('reports.sales.pdf', ['from' => $from, 'to' => $to]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">PDF Export</a>
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
            <article class="stat-card sm:col-span-1">
                <p class="stat-title">Total Revenue</p>
                <p class="stat-value">{{ 'K ' . number_format($totalSales, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Daily Buckets</p>
                <p class="stat-value">{{ $dailyRows->count() }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Monthly Buckets</p>
                <p class="stat-value">{{ $monthlyRows->count() }}</p>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-sm font-medium text-slate-600">Daily Revenue Trend</div>
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Transactions</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailyRows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5]">
                                    <td class="px-4 py-3">{{ $row->period }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->transactions_count }}</td>
                                    <td class="px-4 py-3 text-right">{{ 'K ' . number_format($row->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No daily sales data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-sm font-medium text-slate-600">Monthly Revenue Trend</div>
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Month</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Transactions</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlyRows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5]">
                                    <td class="px-4 py-3">{{ $row->period }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->transactions_count }}</td>
                                    <td class="px-4 py-3 text-right">{{ 'K ' . number_format($row->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No monthly sales data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-sm font-medium text-slate-600">Revenue by Category</div>
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Category</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Transactions</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categoryRows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5]">
                                    <td class="px-4 py-3">{{ $row->category_name }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->transactions_count }}</td>
                                    <td class="px-4 py-3 text-right">{{ 'K ' . number_format($row->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No category sales data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-sm font-medium text-slate-600">Revenue by User</div>
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Transactions</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userRows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5]">
                                    <td class="px-4 py-3">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-right">{{ $row->transactions_count }}</td>
                                    <td class="px-4 py-3 text-right">{{ 'K ' . number_format($row->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No user sales data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>