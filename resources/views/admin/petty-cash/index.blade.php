<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Admin</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Petty Cash Management</h2>
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

        {{-- Allocate Petty Cash --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-bold text-[#0B4D2C]">Allocate Petty Cash for {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h3>
            <form method="POST" action="{{ route('admin.petty-cash.allocate') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Salesperson</label>
                    <select name="user_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="">Select...</option>
                        @foreach ($salespersons as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Amount (K)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="w-full rounded-lg border-slate-300 text-sm" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Note (optional)</label>
                    <input type="text" name="note" maxlength="500" class="w-full rounded-lg border-slate-300 text-sm" placeholder="e.g. April petty cash">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white hover:bg-[#0B4D2C]/90">Allocate</button>
                </div>
            </form>
        </section>

        {{-- Summary Table --}}
        <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-bold text-[#0B4D2C]">Petty Cash Summary — {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h3>
            </div>

            @if ($summary->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="font-medium text-slate-700">No salespersons found</p>
                    <p class="mt-1 text-sm text-slate-500">Create salesperson accounts first.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Salesperson</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Allocated</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Spent</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Balance</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Expenses</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($summary as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row->user->name }}</td>
                                    <td class="px-4 py-3 text-right {{ $row->allocated > 0 ? 'text-[#0B4D2C] font-semibold' : 'text-slate-400' }}">K {{ number_format($row->allocated, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-red-600 font-semibold">K {{ number_format($row->spent, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold {{ $row->balance < 0 ? 'text-red-600' : 'text-[#0B4D2C]' }}">K {{ number_format($row->balance, 2) }}</td>
                                    <td class="px-4 py-3 text-center">{{ $row->expense_count }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.petty-cash.report', ['user' => $row->user->id, 'month' => $month]) }}" class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Daily Report</a>
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
