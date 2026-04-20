<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Petty Cash</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">{{ $user->name }} — Daily Report</h2>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="month" name="month" value="{{ $month }}" class="rounded-lg border-slate-300 text-sm" onchange="this.form.submit()">
                </form>
                <a href="{{ route('admin.petty-cash.index', ['month' => $month]) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Summary Cards --}}
        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Allocated</p>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">K {{ number_format($allocated, 2) }}</p>
                @if ($allocation?->note)
                    <p class="mt-1 text-xs text-slate-500">{{ $allocation->note }}</p>
                @endif
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Total Spent</p>
                <p class="mt-2 text-2xl font-bold text-red-600">K {{ number_format($spent, 2) }}</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Remaining Balance</p>
                <p class="mt-2 text-2xl font-bold {{ $balance < 0 ? 'text-red-600' : 'text-[#0B4D2C]' }}">K {{ number_format($balance, 2) }}</p>
            </article>
        </section>

        {{-- Daily Breakdown --}}
        @if ($dailyBreakdown->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <p class="font-medium text-slate-700">No expenses recorded this month</p>
            </section>
        @else
            @foreach ($dailyBreakdown as $day)
                <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <h3 class="text-sm font-bold text-[#0B4D2C]">{{ $day->date->format('l, d M Y') }}</h3>
                        <span class="rounded-lg bg-red-100 px-3 py-1 text-xs font-bold text-red-700">K {{ number_format($day->total, 2) }}</span>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Category</th>
                                <th class="px-4 py-2 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($day->items as $expense)
                                <tr>
                                    <td class="px-4 py-2 text-slate-700">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-slate-500">{{ $expense->category ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right font-medium text-red-600">K {{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endforeach
        @endif
    </div>
</x-app-layout>
