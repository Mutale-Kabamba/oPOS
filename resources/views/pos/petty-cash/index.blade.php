<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Point of Sale</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Petty Cash</h2>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $month }}" class="rounded-lg border-slate-300 text-sm" onchange="this.form.submit()">
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif

        {{-- Summary Cards --}}
        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Allocated</p>
                <p class="mt-2 text-2xl font-bold text-[#0B4D2C]">K {{ number_format($allocated, 2) }}</p>
                @if (! $allocation)
                    <p class="mt-1 text-xs text-orange-600">No allocation yet this month.</p>
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

        {{-- Record New Expense --}}
        @if ($allocation)
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-bold text-[#0B4D2C]">Record Expense</h3>
                <form method="POST" action="{{ route('pos.petty-cash.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', today()->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Amount (K)</label>
                        <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                        <select name="category" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">General</option>
                            <option value="Transport" @selected(old('category') === 'Transport')>Transport</option>
                            <option value="Meals" @selected(old('category') === 'Meals')>Meals</option>
                            <option value="Supplies" @selected(old('category') === 'Supplies')>Supplies</option>
                            <option value="Communication" @selected(old('category') === 'Communication')>Communication</option>
                            <option value="Other" @selected(old('category') === 'Other')>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <input type="text" name="description" value="{{ old('description') }}" maxlength="255" class="w-full rounded-lg border-slate-300 text-sm" placeholder="What was it for?" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white hover:bg-[#0B4D2C]/90">Add Expense</button>
                    </div>
                </form>
            </section>
        @endif

        {{-- Daily Breakdown --}}
        @if ($dailyBreakdown->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <p class="font-medium text-slate-700">No expenses recorded this month</p>
                <p class="mt-1 text-sm text-slate-500">Record your daily expenses above.</p>
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
                                <th class="px-4 py-2 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($day->items as $expense)
                                <tr>
                                    <td class="px-4 py-2 text-slate-700">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-slate-500">{{ $expense->category ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right font-medium text-red-600">K {{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <form method="POST" action="{{ route('pos.petty-cash.destroy', $expense) }}" class="inline" onsubmit="return confirm('Delete this expense?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endforeach
        @endif
    </div>
</x-app-layout>
