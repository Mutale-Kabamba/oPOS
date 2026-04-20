<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Balance Sheet</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">
                    <span class="sm:hidden">Back</span>
                    <span class="hidden sm:inline">Back to Reports</span>
                </a>
                <a href="{{ route('reports.balance-sheet.pdf', ['as_of' => $asOf]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">
                    <span class="sm:hidden">PDF</span>
                    <span class="hidden sm:inline">PDF Export</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl space-y-4">
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm text-slate-600">As Of</label>
                    <input type="date" name="as_of" value="{{ $asOf }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <button class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]" type="submit">
                    <span class="sm:hidden">Apply</span>
                    <span class="hidden sm:inline">Apply Filter</span>
                </button>
            </form>
        </section>

        <section class="stat-grid sm:grid-cols-3">
            <article class="stat-card">
                <p class="stat-title">Total Valuables</p>
                <p class="stat-value">{{ 'K ' . number_format($totalValuables, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Total Debts</p>
                <p class="stat-value">{{ 'K ' . number_format($totalDebts, 2) }}</p>
            </article>
            <article class="stat-card">
                <p class="stat-title">Equity</p>
                <p class="stat-value {{ $equity >= 0 ? 'text-green-700' : 'text-orange-700' }}">{{ 'K ' . number_format($equity, 2) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-sm">
            Snapshot as of {{ \Carbon\Carbon::parse($asOf)->format('d M Y') }}. Equation check: Valuables ({{ 'K ' . number_format($totalValuables, 2) }}) = Debts + Equity ({{ 'K ' . number_format($totalDebts + $equity, 2) }}).
            @if (abs($equationGap) > 0.0001)
                <span class="font-medium text-orange-700"> Difference detected: {{ 'K ' . number_format($equationGap, 2) }}</span>
            @endif
        </section>
    </div>
</x-app-layout>
