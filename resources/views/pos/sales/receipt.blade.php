<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Receipt — {{ $sale->sale_number }}</h2>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Print Receipt</button>
                <a href="{{ route('pos.sell') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">New Sale</a>
                <a href="{{ route('pos.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Dashboard</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
    @endif

    <div class="max-w-2xl mx-auto">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm print:shadow-none print:border-0">
            <div class="text-center border-b border-slate-200 pb-4 mb-4">
                <h3 class="text-lg font-bold text-[#0B4D2C]">oPOS | By Ori</h3>
                <p class="text-sm text-slate-500">Sales Receipt</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <p class="text-slate-500">Sale Number</p>
                    <p class="font-medium text-slate-700">{{ $sale->sale_number }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Date</p>
                    <p class="font-medium text-slate-700">{{ $sale->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Cashier</p>
                    <p class="font-medium text-slate-700">{{ $sale->user->name }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Payment</p>
                    <p class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</p>
                </div>
            </div>

            <table class="w-full text-sm mb-4">
                <thead class="border-y border-slate-200">
                    <tr>
                        <th class="py-2 text-left text-xs font-bold uppercase text-slate-500">Item</th>
                        <th class="py-2 text-center text-xs font-bold uppercase text-slate-500">Qty</th>
                        <th class="py-2 text-right text-xs font-bold uppercase text-slate-500">Price</th>
                        <th class="py-2 text-right text-xs font-bold uppercase text-slate-500">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-700">{{ $item->product->name }}</td>
                            <td class="py-2 text-center text-slate-700">{{ $item->quantity }}</td>
                            <td class="py-2 text-right text-slate-700">K {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 text-right font-medium text-slate-900">K {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-between items-center border-t-2 border-[#0B4D2C] pt-3">
                <span class="text-lg font-bold text-[#0B4D2C]">TOTAL</span>
                <span class="text-xl font-bold text-[#0B4D2C]">K {{ number_format($sale->total, 2) }}</span>
            </div>

            @if ($sale->notes)
                <div class="mt-4 rounded-lg bg-[#F4F7F5] p-3">
                    <p class="text-xs text-slate-500">Notes</p>
                    <p class="text-sm text-slate-700">{{ $sale->notes }}</p>
                </div>
            @endif

            <div class="mt-6 text-center text-xs text-slate-400">
                <p>Thank you for your purchase!</p>
            </div>
        </div>
    </div>
</x-app-layout>
