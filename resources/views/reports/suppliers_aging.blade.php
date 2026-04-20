<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Suppliers Balance Report</h2>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Back to Reports</a>
                <a href="{{ route('reports.suppliers-aging.pdf', ['as_of' => $asOf]) }}" class="inline-flex items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35]">PDF Export</a>
            </div>
        </div>
    </x-slot>

    <div
        class="max-w-6xl space-y-4"
        x-data="{
            selectedDebtId: @js(old('debt_id')),
            debts: @js($debts->mapWithKeys(fn($debt) => [
                (string) $debt->id => [
                    'id' => (string) $debt->id,
                    'supplier_name' => $debt->supplier?->name ?? 'Unknown Supplier',
                    'account_name' => $debt->account_name ?? '—',
                    'description' => $debt->description ?: 'No description',
                    'remaining_amount' => number_format($debt->remaining_amount, 2, '.', ''),
                    'payment_url' => route('reports.suppliers-aging.payments', $debt->id),
                ],
            ])),
            openPaymentModal(id) {
                this.selectedDebtId = String(id);
                this.$dispatch('open-modal', 'record-supplier-payment');
            },
            get selectedDebt() {
                return this.selectedDebtId ? (this.debts[this.selectedDebtId] ?? null) : null;
            }
        }"
        x-init="if (selectedDebtId) { $dispatch('open-modal', 'record-supplier-payment'); }"
    >
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm text-slate-600">As Of</label>
                    <input type="date" name="as_of" value="{{ $asOf }}" class="rounded-lg border-slate-300 text-sm">
                </div>
                <button class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]" type="submit">Apply Filter</button>
            </form>
        </section>

        <section class="stat-grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <article class="stat-card xl:col-span-2">
                <p class="stat-title">Total Due</p>
                <p class="stat-value">{{ 'K ' . number_format($totals['total_due'], 2) }}</p>
            </article>
            <article class="stat-card"><p class="stat-title">Current</p><p class="stat-value-sm">{{ 'K ' . number_format($totals['current'], 2) }}</p></article>
            <article class="stat-card"><p class="stat-title">1-30 Days</p><p class="stat-value-sm">{{ 'K ' . number_format($totals['days_1_30'], 2) }}</p></article>
            <article class="stat-card"><p class="stat-title">31-60 Days</p><p class="stat-value-sm">{{ 'K ' . number_format($totals['days_31_60'], 2) }}</p></article>
            <article class="stat-card"><p class="stat-title">61-90 Days</p><p class="stat-value-sm">{{ 'K ' . number_format($totals['days_61_90'], 2) }}</p></article>
            <article class="stat-card"><p class="stat-title">90+ Days</p><p class="stat-value-sm">{{ 'K ' . number_format($totals['days_90_plus'], 2) }}</p></article>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="table-shell">
                <table class="table-compact min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Contact</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Current</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">1-30</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">31-60</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">61-90</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">90+</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Total Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $row->supplier_name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->contact_person ?: '—' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->current > 0 ? 'K ' . number_format($row->current, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->days_1_30 > 0 ? 'K ' . number_format($row->days_1_30, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->days_31_60 > 0 ? 'K ' . number_format($row->days_31_60, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->days_61_90 > 0 ? 'K ' . number_format($row->days_61_90, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->days_90_plus > 0 ? 'K ' . number_format($row->days_90_plus, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-800">{{ 'K ' . number_format($row->total_due, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-500">No unpaid supplier liabilities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-800">Outstanding Supplier Debts</h3>
                <p class="mt-1 text-xs text-slate-500">Record partial or full payments against each open supplier liability.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="table-compact min-w-full table-auto text-sm">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col class="w-[8.5rem]">
                        <col class="w-[8.5rem]">
                        <col class="w-[8.5rem]">
                        <col>
                        <col>
                    </colgroup>
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Supplier</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Liability Account</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Description</th>
                            <th class="w-[8.5rem] px-3 py-2 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Original</th>
                            <th class="w-[8.5rem] px-3 py-2 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Paid</th>
                            <th class="w-[8.5rem] px-3 py-2 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Remaining</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($debts as $debt)
                            <tr class="odd:bg-white even:bg-[#F4F7F5] align-top hover:bg-slate-100/70">
                                <td class="px-3 py-2 font-medium text-slate-800">{{ $debt->supplier?->name ?? 'Unknown Supplier' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-slate-700">{{ optional($debt->date)->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ $debt->account_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ $debt->description ?: '—' }}</td>
                                <td class="w-[8.5rem] px-3 py-2 text-right whitespace-nowrap tabular-nums">{{ 'K ' . number_format($debt->original_amount, 2) }}</td>
                                <td class="w-[8.5rem] px-3 py-2 text-right whitespace-nowrap tabular-nums">{{ $debt->paid_amount > 0 ? 'K ' . number_format($debt->paid_amount, 2) : '—' }}</td>
                                <td class="w-[8.5rem] px-3 py-2 text-right whitespace-nowrap font-medium text-slate-800 tabular-nums">{{ 'K ' . number_format($debt->remaining_amount, 2) }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{{ str_replace('_', ' ', ucfirst($debt->payment_status)) }}</span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <button
                                        type="button"
                                        x-on:click="openPaymentModal('{{ $debt->id }}')"
                                        class="inline-flex items-center justify-center rounded-lg bg-[#32CD32] px-3 py-1.5 text-xs font-medium text-[#0B4D2C]"
                                    >
                                        Pay
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-6 text-center text-slate-500">No outstanding supplier debts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($errors->has('amount') || $errors->has('date') || $errors->has('description'))
                <div class="border-t border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('amount') ?: ($errors->first('date') ?: $errors->first('description')) }}
                </div>
            @endif
        </section>

        <x-modal name="record-supplier-payment" :show="$errors->has('amount') || $errors->has('date') || $errors->has('description')" maxWidth="lg" focusable>
            <form x-bind:action="selectedDebt ? selectedDebt.payment_url : '#'
                " method="POST" class="p-6">
                @csrf
                <input type="hidden" name="as_of" value="{{ $asOf }}">
                <input type="hidden" name="debt_id" x-model="selectedDebtId">

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Record Supplier Payment</h3>
                        <p class="mt-1 text-sm text-slate-500">Capture a partial or full payment against the selected supplier debt.</p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-600">Close</button>
                </div>

                <div class="mt-5 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Supplier</p>
                        <p class="mt-1 text-sm font-medium text-slate-800" x-text="selectedDebt ? selectedDebt.supplier_name : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount Due</p>
                        <p class="mt-1 text-sm font-medium text-slate-800" x-text="selectedDebt ? `K ${Number(selectedDebt.remaining_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Liability Account</p>
                        <p class="mt-1 text-sm text-slate-700" x-text="selectedDebt ? selectedDebt.account_name : '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Description</p>
                        <p class="mt-1 text-sm text-slate-700" x-text="selectedDebt ? selectedDebt.description : '—'"></p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="payment_amount" class="mb-1 block text-sm font-medium text-slate-700">Amount</label>
                        <input id="payment_amount" type="number" name="amount" step="0.01" min="0.01" x-bind:max="selectedDebt ? selectedDebt.remaining_amount : null" value="{{ old('amount') }}" class="block w-full rounded-lg border-slate-300 text-sm" required>
                    </div>
                    <div>
                        <label for="payment_date" class="mb-1 block text-sm font-medium text-slate-700">Payment Date</label>
                        <input id="payment_date" type="date" name="date" value="{{ old('date', $asOf) }}" class="block w-full rounded-lg border-slate-300 text-sm" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="payment_note" class="mb-1 block text-sm font-medium text-slate-700">Optional Note</label>
                    <textarea id="payment_note" name="description" rows="3" class="block w-full rounded-lg border-slate-300 text-sm">{{ old('description') }}</textarea>
                </div>

                @if ($errors->has('amount') || $errors->has('date') || $errors->has('description'))
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('amount') ?: ($errors->first('date') ?: $errors->first('description')) }}
                    </div>
                @endif

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" x-on:click="$dispatch('close')" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Save Payment</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>