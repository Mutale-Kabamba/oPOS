<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Sales History</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pos.sell') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">+ New Sale</a>
                <a href="{{ route('pos.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('pos.sales.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Filter by Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <button type="submit" class="rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Filter</button>
                @if (request('date'))
                    <a href="{{ route('pos.sales.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Clear</a>
                @endif
            </form>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if ($sales->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No sales found</p>
                    <p class="mt-1 text-sm text-slate-500">Sales will appear here once completed.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Sale #</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date & Time</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Payment</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Notes</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $sale->sale_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $sale->items->sum('quantity') }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ 'K ' . number_format($sale->total, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                                    <td class="px-4 py-3 text-slate-500 cell-truncate">{{ $sale->notes ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('pos.receipt', $sale) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Receipt</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-200">
                    {{ $sales->withQueryString()->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
