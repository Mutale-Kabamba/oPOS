<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin Console</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Inventory &amp; Sales Overview</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
                <a href="{{ route('admin.pos-products.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">
                    <span class="sm:hidden">+ Product</span>
                    <span class="hidden sm:inline">+ Add Product</span>
                </a>
                <a href="{{ route('admin.pos-products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">
                    <span class="sm:hidden">Inventory</span>
                    <span class="hidden sm:inline">All Products</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="stat-grid sm:grid-cols-2 xl:grid-cols-3">
            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">Active Products</p>
                    <span class="stat-icon bg-sky-100 text-sky-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ number_format($totalProducts) }}</p>
            </article>

            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">Low Stock Items</p>
                    <span class="stat-icon {{ $lowStockCount > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3l9 16H3L12 3Z"/></svg>
                    </span>
                </div>
                <p class="stat-value {{ $lowStockCount > 0 ? 'text-red-600' : '' }}">{{ number_format($lowStockCount) }}</p>
            </article>

            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">Today's Sales</p>
                    <span class="stat-icon bg-green-100 text-green-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ number_format($todaySales) }} <span class="text-sm font-normal text-slate-500">/ K {{ number_format($todayRevenue, 2) }}</span></p>
            </article>

            <article class="stat-card">
                <div class="stat-head">
                    <p class="stat-title">This Month's Sales</p>
                    <span class="stat-icon bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 14l3-3 3 2 4-5"/></svg>
                    </span>
                </div>
                <p class="stat-value">{{ number_format($monthSales) }} <span class="text-sm font-normal text-slate-500">/ K {{ number_format($monthRevenue, 2) }}</span></p>
            </article>
        </section>

        @if ($lowStockProducts->isNotEmpty())
        <section class="rounded-xl border border-red-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-red-200 bg-red-50 px-4 py-3">
                <h3 class="text-sm font-bold text-red-700">Low Stock Alert</h3>
                <a href="{{ route('admin.pos-products.index') }}" class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">View All Products</a>
            </div>
            <div class="table-shell">
                <table class="table-compact min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowStockProducts as $product)
                            <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                <td class="cell-truncate px-4 py-3 font-medium text-slate-700">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->sku ?? '—' }}</td>
                                <td class="px-4 py-3 font-bold {{ $product->stock <= 0 ? 'text-red-600' : 'text-orange-600' }}">{{ $product->stock }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">K {{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.pos-products.edit', $product) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Restock</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Recent POS Sales</h3>
                <a href="{{ route('reports.sales') }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">
                    <span class="sm:hidden">Report</span>
                    <span class="hidden sm:inline">Sales Report</span>
                </a>
            </div>

            @if ($recentSales->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No sales yet</p>
                    <p class="mt-1 text-sm text-slate-500">POS sales will appear here once recorded by salespersons.</p>
                </div>
            @else
                <div x-data="{ showAll: false }">
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Sale #</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Salesperson</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSales as $sale)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70" x-show="showAll || {{ $loop->index }} < 5">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $sale->sale_number }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $sale->user?->name ?? 'Unknown' }}</td>
                                    <td class="cell-truncate px-4 py-3 text-slate-600">{{ $sale->items->count() }} item(s)</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">K {{ number_format($sale->total, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-recent-table-footer :total="$recentSales->count()" />
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
