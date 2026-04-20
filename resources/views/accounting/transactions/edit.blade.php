<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Accounting Workspace</p>
            <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Edit Financial Transaction</h2>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">{{ session('status') }}</div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            x-data="{
                selectedType: @js(old('transaction_type', $transactionType)),
                selectedAccount: @js((string) old('account_id', (string) $transaction->account_id)),
                accounts: @js($accounts->map(fn($account) => [
                    'id' => (string) $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                ])->values()),
                selectedSupplier: @js((string) old('supplier_id', (string) $transaction->supplier_id)),
                suppliers: @js($suppliers->map(fn($supplier) => [
                    'id' => (string) $supplier->id,
                    'name' => $supplier->name,
                ])->values()),
                selectedPaymentStatus: @js(old('payment_status', $transaction->payment_status)),
                typeMap: @js($transactionTypeMap),
                typeLabels: {
                    income: 'Income',
                    cogs: 'COGS',
                    expense: 'Expense',
                    asset: 'Asset',
                    liability: 'Liability'
                },
                get filteredAccounts() {
                    const expected = this.typeMap[this.selectedType];
                    if (!expected) return [];
                    return this.accounts.filter(account => account.type === expected);
                },
                get expectedTypeLabel() {
                    const expected = this.typeMap[this.selectedType];
                    if (!expected) return '';
                    return this.typeLabels[expected] ?? expected.toUpperCase();
                },
                syncAccountSelection() {
                    const isValid = this.filteredAccounts.some(account => account.id === this.selectedAccount);
                    if (!isValid) this.selectedAccount = '';
                    if (this.selectedType !== 'debts') this.selectedSupplier = '';
                }
            }"
            x-init="syncAccountSelection()"
        >
            <form method="POST" action="{{ route('accounting.transactions.update', $transaction) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="payment_status" :value="selectedPaymentStatus">

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Transaction Type</label>
                    <select
                        name="transaction_type"
                        x-model="selectedType"
                        @change="syncAccountSelection()"
                        class="block w-full rounded-lg border-slate-300 text-sm"
                        required
                    >
                        <option value="">Select transaction type</option>
                        @foreach ($transactionTypeOptions as $option)
                            <option value="{{ $option['value'] }}" @selected(old('transaction_type', $transactionType) === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <p x-show="selectedType" class="mt-1 text-xs text-slate-500">
                        Smart mapping: this will show only
                        <span class="font-medium text-[#0B4D2C]" x-text="expectedTypeLabel"></span>
                        ledger accounts.
                    </p>
                    @error('transaction_type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Amount</label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" class="block w-full rounded-lg border-slate-300 text-sm" required>
                    @error('amount')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Date</label>
                    <input type="date" name="date" value="{{ old('date', optional($transaction->date)->toDateString()) }}" class="block w-full rounded-lg border-slate-300 text-sm" required>
                    @error('date')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Ledger Account</label>
                    <select name="account_id" x-model="selectedAccount" class="block w-full rounded-lg border-slate-300 text-sm" :disabled="!selectedType" required>
                        <option value="">Select an account</option>
                        <template x-for="account in filteredAccounts" :key="account.id">
                            <option :value="account.id" x-text="`${account.code} - ${account.name} (${account.type.toUpperCase()})`"></option>
                        </template>
                    </select>
                    @error('account_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    <p x-show="selectedType && filteredAccounts.length === 0" class="mt-1 text-xs text-orange-700">No active accounts available for the selected type.</p>
                </div>

                <div x-show="selectedType === 'debts'" x-cloak>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Supplier</label>
                    <select name="supplier_id" x-model="selectedSupplier" class="block w-full rounded-lg border-slate-300 text-sm" :required="selectedType === 'debts'">
                        <option value="">Select a supplier</option>
                        <template x-for="supplier in suppliers" :key="supplier.id">
                            <option :value="supplier.id" x-text="supplier.name"></option>
                        </template>
                    </select>
                    @error('supplier_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    <p x-show="selectedType === 'debts' && suppliers.length === 0" class="mt-1 text-xs text-orange-700">No active suppliers found. Create a supplier record before posting payables.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Payment Status</label>
                    <select x-model="selectedPaymentStatus" class="block w-full rounded-lg border-slate-300 text-sm" :disabled="selectedType === 'debts'" required>
                        <option value="pending">Pending</option>
                        <option value="partially_paid">Partially Paid</option>
                        <option value="paid">Paid</option>
                    </select>
                    <p x-show="selectedType === 'debts'" class="mt-1 text-xs text-slate-500">Debt statuses are calculated from recorded payments and cannot be edited directly.</p>
                    @error('payment_status')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea name="description" rows="4" class="block w-full rounded-lg border-slate-300 text-sm">{{ old('description', $transaction->description) }}</textarea>
                    @error('description')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-[#32CD32] px-4 py-2 text-sm font-medium text-[#0B4D2C]">Update Entry</button>
                    <a href="{{ route('reports.transactions') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
