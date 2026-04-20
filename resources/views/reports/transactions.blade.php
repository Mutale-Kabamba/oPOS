<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Reports</p>
                <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Transaction Report</h2>
            </div>
            <div class="w-full sm:w-auto">
                <form method="GET" action="{{ route('reports.transactions.pdf') }}" class="grid grid-cols-1 gap-2 sm:flex sm:items-center">
                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">
                    <input type="hidden" name="q" value="{{ $q ?? '' }}">
                    <select name="report_type" class="w-full rounded-lg border-slate-300 text-sm sm:w-auto">
                        <option value="full">Full PDF</option>
                        <option value="income">Income PDF</option>
                        <option value="expense">Expenses PDF</option>
                    </select>
                    <button class="inline-flex w-full items-center justify-center rounded-lg border border-[#FF6B35] px-4 py-2 text-sm font-medium text-[#FF6B35] sm:w-auto" type="submit">
                        <span class="sm:hidden">Export</span>
                        <span class="hidden sm:inline">Export PDF</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <div x-data="{ showTemplateGuide: false }" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('accounting.transactions.import') }}" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Bulk Import Transactions (Excel/CSV)</label>
                    <input type="file" name="import_file" accept=".xlsx,.xls,.csv" class="w-full rounded-lg border-slate-300 text-sm" required>
                    <p class="mt-1 text-xs text-slate-500">Required headers: transaction_type, amount, date, account_code. Optional: supplier_name, payment_status, description.</p>
                    <p class="text-xs text-slate-500">transaction_type values: money_in, money_out_direct, money_out_general, valuables, debts.</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ route('accounting.transactions.template', ['format' => 'csv']) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Download CSV Template</a>
                        <a href="{{ route('accounting.transactions.template', ['format' => 'xlsx']) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Download XLSX Template</a>
                        <button type="button" @click="showTemplateGuide = true" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Template Guide</button>
                    </div>
                    @error('import_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Import</button>
            </form>

            <div x-show="showTemplateGuide" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4" @click.self="showTemplateGuide = false">
                <div class="w-full max-w-2xl rounded-xl bg-white p-5 shadow-xl">
                    <div class="flex items-start justify-between">
                        <h3 class="text-base font-semibold text-slate-900">Transaction Template Guide</h3>
                        <button type="button" @click="showTemplateGuide = false" class="rounded-md p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700">✕</button>
                    </div>
                    <div class="mt-3 space-y-3 text-sm text-slate-700">
                        <p><span class="font-semibold">Required columns:</span> transaction_type, amount, date, account_code</p>
                        <p><span class="font-semibold">Optional columns:</span> supplier_name, payment_status, description</p>
                        <p><span class="font-semibold">transaction_type values:</span> money_in, money_out_direct, money_out_general, valuables, debts</p>
                        <p><span class="font-semibold">Important:</span> account_code must exist and match the expected account type; debt rows require supplier_name.</p>
                        <p><span class="font-semibold">payment_status:</span> pending or paid for non-debts, partially_paid only for debts.</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="showTemplateGuide = false" class="inline-flex items-center rounded-lg bg-[#0B4D2C] px-4 py-2 text-sm font-medium text-white">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" class="grid grid-cols-1 gap-4 sm:flex sm:flex-wrap sm:items-end">
                <div class="w-full sm:w-auto">
                    <label class="block text-sm text-slate-600">Search</label>
                    <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Description, account, user..." class="w-full rounded-lg border-slate-300 text-sm sm:w-auto">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-sm text-slate-600">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="w-full rounded-lg border-slate-300 text-sm sm:w-auto">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-sm text-slate-600">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="w-full rounded-lg border-slate-300 text-sm sm:w-auto">
                </div>
                <button class="inline-flex w-full items-center justify-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C] sm:w-auto" type="submit">
                    <span class="sm:hidden">Apply</span>
                    <span class="hidden sm:inline">Apply Filter</span>
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if ($rows->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h8"/></svg>
                    </div>
                    <p class="font-medium text-slate-700">No matching transactions</p>
                    <p class="mt-1 text-sm text-slate-500">Try widening the date range or updating your search.</p>
                </div>
            @else
                <div class="table-shell">
                    <table class="table-compact min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Account</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">User</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="odd:bg-white even:bg-[#F4F7F5] hover:bg-slate-100/70">
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $row->date->format('d M Y') }}</td>
                                    <td class="cell-truncate px-4 py-3 font-medium text-slate-700">{{ $row->account?->code }} - {{ $row->account?->name }}</td>
                                    <td class="px-4 py-3">
                                        @if ($row->account?->type === 'income')
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Income</span>
                                        @elseif ($row->account?->type === 'cogs')
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">COGS</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{{ strtoupper($row->account?->type ?? 'n/a') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ 'K ' . number_format($row->displayAmount(), 2) }}</td>
                                    <td class="px-4 py-3">
                                        @if ($row->payment_status === 'paid')
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Paid</span>
                                        @elseif ($row->payment_status === 'partially_paid')
                                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Partially Paid</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $row->user->name }}</td>
                                    <td class="px-4 py-3">
                                        @if (auth()->user()->isAdmin() || (int) auth()->id() === (int) $row->user_id)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('accounting.transactions.edit', $row) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                                <form method="POST" action="{{ route('accounting.transactions.destroy', $row) }}" data-confirm-title="Delete transaction?" data-confirm-message="This action cannot be undone." data-confirm-confirm-text="Delete" data-confirm-variant="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
