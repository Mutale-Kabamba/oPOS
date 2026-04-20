<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">POS Dashboard</h2>
            </div>
            <a href="{{ route('pos.sell') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">
                <span class="sm:hidden">+ Sale</span>
                <span class="hidden sm:inline">+ New Sale</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Today's Sales</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V3m0 0L12 7m4-4 4 4"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 11h4v10H3zM9 8h4v13H9zM15 14h4v7h-4z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">{{ $todaySales }}</p>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Today's Revenue</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m5-7H7"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">{{ 'K ' . number_format($todayRevenue, 2) }}</p>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Monthly Sales</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">{{ $monthSales }}</p>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Monthly Revenue</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l6-6 4 4 6-6"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">{{ 'K ' . number_format($monthRevenue, 2) }}</p>
            </article>
        </section>

        {{-- Petty Cash Summary --}}
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Petty Cash — {{ now()->format('F Y') }}</h3>
                <a href="{{ route('pos.petty-cash.index') }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">View Details</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs text-slate-500">Allocated</p>
                    <p class="text-lg font-bold text-[#0B4D2C]">K {{ number_format($pettyCashAllocated, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Spent</p>
                    <p class="text-lg font-bold text-red-600">K {{ number_format($pettyCashSpent, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Remaining</p>
                    <p class="text-lg font-bold {{ $pettyCashBalance < 0 ? 'text-red-600' : 'text-[#0B4D2C]' }}">K {{ number_format($pettyCashBalance, 2) }}</p>
                </div>
            </div>
        </section>

        @if ($lowStockProducts->isNotEmpty())
            <section class="rounded-xl border border-orange-200 bg-orange-50 p-4 shadow-sm">
                <h3 class="text-sm font-bold text-orange-800 mb-2">Low Stock Alert</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($lowStockProducts as $product)
                        <span class="inline-flex items-center rounded-lg bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700">
                            {{ $product->name }} — {{ $product->stock }} left
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Recent Sales</h3>
                <a href="{{ route('pos.sales.index') }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">View All</a>
            </div>

            @if ($recentSales->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No sales yet</p>
                    <p class="mt-1 text-sm text-slate-500">Start your first sale to see data here.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Sale #</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Payment</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSales as $sale)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $sale->sale_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $sale->items->sum('quantity') }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ 'K ' . number_format($sale->total, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('pos.receipt', $sale) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Receipt</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
