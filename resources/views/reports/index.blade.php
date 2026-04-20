<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Choose Statement</h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">
        <section class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-sm">
            Select which financial statement you want to open.
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('reports.income-statement') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Statement</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Income Statement</h3>
                        <p class="mt-2 text-sm text-slate-600">View income, direct costs, expenses, and net profit by period.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ route('reports.balance-sheet') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Statement</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Balance Sheet</h3>
                        <p class="mt-2 text-sm text-slate-600">Review valuables, debts, equity, and equation consistency as of a date.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-orange-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M8 9h8M8 13h8"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ route('reports.trial-balance') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Audit</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Trial Balance</h3>
                        <p class="mt-2 text-sm text-slate-600">Review closing debit and credit balances for every active ledger account.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 7h14M5 12h14M5 17h14"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ route('reports.suppliers-aging') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Payables</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Suppliers Balance</h3>
                        <p class="mt-2 text-sm text-slate-600">Track outstanding supplier balances by aging bucket for unpaid liabilities.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ route('reports.sales') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Revenue</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Sales Reports</h3>
                        <p class="mt-2 text-sm text-slate-600">Break down money-in transactions by trend, category, and contributing user.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 14l3-3 3 2 4-5"/></svg>
                    </span>
                </div>
            </a>

            <a href="{{ route('reports.reconciliation') }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#32CD32] hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Controls</p>
                        <h3 class="mt-1 text-lg font-bold text-[#0B4D2C]">Reconciliation</h3>
                        <p class="mt-2 text-sm text-slate-600">Match internal asset account activity against bank statement balances and clear items.</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/></svg>
                    </span>
                </div>
            </a>
        </section>
    </div>
</x-app-layout>
