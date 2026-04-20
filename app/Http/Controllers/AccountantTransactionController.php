<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Services\SpreadsheetReaderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountantTransactionController extends Controller
{
    public function __construct(private readonly SpreadsheetReaderService $spreadsheetReader)
    {
    }

    public function create()
    {
        $accounts = $this->accountsQuery()->get();
        $suppliers = $this->suppliersQuery()->get();
        $transactionTypeOptions = $this->transactionTypeOptions();
        $transactionTypeMap = StoreTransactionRequest::TYPE_MAP;

        return view('accounting.transactions.create', compact('accounts', 'suppliers', 'transactionTypeOptions', 'transactionTypeMap'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $accounts = $this->accountsQuery()->orWhere('id', $transaction->account_id)->get();
        $suppliers = $this->suppliersQuery()->orWhere('id', $transaction->supplier_id)->get();
        $transactionTypeOptions = $this->transactionTypeOptions();
        $transactionTypeMap = StoreTransactionRequest::TYPE_MAP;

        $transactionType = collect(StoreTransactionRequest::TYPE_MAP)
            ->search($transaction->account?->type, strict: true) ?: 'money_in';

        return view('accounting.transactions.edit', compact('transaction', 'accounts', 'suppliers', 'transactionTypeOptions', 'transactionTypeMap', 'transactionType'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $transactionType = (string) $validated['transaction_type'];
        $account = Account::query()->findOrFail((int) $validated['account_id']);

        $validated['user_id'] = auth()->id();
        $validated['payment_status'] = $this->resolveStoredPaymentStatus($transactionType, null, (string) $validated['payment_status']);
        $validated['metadata'] = [
            'source' => 'manual-entry',
            'created_by_role' => auth()->user()->role,
            'transaction_type' => $validated['transaction_type'],
            'expected_account_type' => $request->expectedAccountType(),
        ];
        unset($validated['transaction_type']);

        $transaction = Transaction::create($validated);
        $this->syncDebtStatusIfNeeded($transaction);

        $entryTypeLabel = $this->transactionTypeLabel($transactionType, $account->type);
        $amount = $this->formatAmount((float) $transaction->amount);
        $ledger = $account->name;

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'entry_create',
            'description' => "Created: {$entryTypeLabel} > {$ledger} > {$amount}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        return back()->with('status', 'Transaction recorded successfully.');
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);

        $validated = $request->validated();
        $transactionType = (string) $validated['transaction_type'];
        $account = Account::query()->findOrFail((int) $validated['account_id']);

        $metadata = $transaction->metadata ?? [];
        $metadata['last_updated_by'] = auth()->id();
        $metadata['transaction_type'] = $validated['transaction_type'];
        $metadata['expected_account_type'] = $request->expectedAccountType();

        $paymentStatus = $this->resolveStoredPaymentStatus($transactionType, $transaction, (string) $validated['payment_status']);

        $transaction->update([
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'account_id' => $validated['account_id'],
            'supplier_id' => $validated['supplier_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'payment_status' => $paymentStatus,
            'metadata' => $metadata,
        ]);

        $this->syncDebtStatusIfNeeded($transaction->fresh());

        $entryTypeLabel = $this->transactionTypeLabel($transactionType, $account->type);
        $amount = $this->formatAmount((float) $transaction->amount);
        $ledger = $account->name;

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'entry_update',
            'description' => "Edited: {$entryTypeLabel} > {$ledger} > {$amount}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        return redirect()->route('reports.transactions')->with('status', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $accountType = $transaction->account?->type;
        $transactionType = $transaction->metadata['transaction_type'] ?? null;
        $entryTypeLabel = $this->transactionTypeLabel(is_string($transactionType) ? $transactionType : null, $accountType);
        $ledger = $transaction->account?->name ?? 'Unknown account';
        $amount = $this->formatAmount((float) $transaction->amount);

        $transaction->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'entry_delete',
            'description' => "Deleted: {$entryTypeLabel} > {$ledger} > {$amount}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        return back()->with('status', 'Transaction deleted successfully.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $rows = $this->spreadsheetReader->readRows($request->file('import_file'));

        if (count($rows) === 0) {
            return back()->with('status', 'Import file is empty.');
        }

        $accountsByCode = Account::query()->where('is_active', true)->get()->keyBy(fn (Account $account) => strtolower($account->code));
        $suppliersByName = Supplier::query()->where('is_active', true)->get()->keyBy(fn (Supplier $supplier) => strtolower($supplier->name));

        $errors = [];
        $created = 0;

        try {
            DB::transaction(function () use ($rows, $accountsByCode, $suppliersByName, &$errors, &$created): void {
                foreach ($rows as $row) {
                    $rowNumber = (int) ($row['__row'] ?? 0);

                    $payload = [
                        'transaction_type' => $this->normalizeValue($row['transaction_type'] ?? null),
                        'amount' => $row['amount'] ?? null,
                        'date' => $this->normalizeDate($row['date'] ?? null),
                        'account_code' => $this->normalizeValue($row['account_code'] ?? null),
                        'supplier_name' => $this->normalizeValue($row['supplier_name'] ?? null),
                        'description' => $row['description'] ?? null,
                        'payment_status' => $this->normalizeValue($row['payment_status'] ?? null) ?: Transaction::PAYMENT_STATUS_PENDING,
                    ];

                    $validator = Validator::make($payload, [
                        'transaction_type' => ['required', Rule::in(array_keys(StoreTransactionRequest::TYPE_MAP))],
                        'amount' => ['required', 'numeric', 'min:0.01'],
                        'date' => ['required', 'date'],
                        'account_code' => ['required', 'string'],
                        'supplier_name' => ['nullable', 'string'],
                        'description' => ['nullable', 'string', 'max:1000'],
                        'payment_status' => ['required', Rule::in([
                            Transaction::PAYMENT_STATUS_PENDING,
                            Transaction::PAYMENT_STATUS_PARTIALLY_PAID,
                            Transaction::PAYMENT_STATUS_PAID,
                        ])],
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: {$validator->errors()->first()}";

                        continue;
                    }

                    $validated = $validator->validated();
                    $expectedType = StoreTransactionRequest::TYPE_MAP[$validated['transaction_type']];
                    $account = $accountsByCode->get(strtolower($validated['account_code']));

                    if (! $account) {
                        $errors[] = "Row {$rowNumber}: Unknown or inactive account_code '{$validated['account_code']}'.";

                        continue;
                    }

                    if ($account->type !== $expectedType) {
                        $errors[] = "Row {$rowNumber}: account_code '{$validated['account_code']}' is {$account->type}, expected {$expectedType}.";

                        continue;
                    }

                    $supplier = null;

                    if ($expectedType === 'liability') {
                        if (empty($validated['supplier_name'])) {
                            $errors[] = "Row {$rowNumber}: supplier_name is required for debts.";

                            continue;
                        }

                        $supplier = $suppliersByName->get(strtolower($validated['supplier_name']));

                        if (! $supplier) {
                            $errors[] = "Row {$rowNumber}: Unknown or inactive supplier_name '{$validated['supplier_name']}'.";

                            continue;
                        }
                    }

                    if ($expectedType !== 'liability' && $validated['payment_status'] === Transaction::PAYMENT_STATUS_PARTIALLY_PAID) {
                        $errors[] = "Row {$rowNumber}: partially_paid is allowed only for debts.";

                        continue;
                    }

                    $paymentStatus = $expectedType === 'liability'
                        ? Transaction::PAYMENT_STATUS_PENDING
                        : $validated['payment_status'];

                    $transaction = Transaction::query()->create([
                        'amount' => $validated['amount'],
                        'date' => $validated['date'],
                        'account_id' => $account->id,
                        'supplier_id' => $supplier?->id,
                        'description' => $validated['description'] ?: null,
                        'payment_status' => $paymentStatus,
                        'user_id' => auth()->id(),
                        'metadata' => [
                            'source' => 'bulk-import',
                            'created_by_role' => auth()->user()->role,
                            'transaction_type' => $validated['transaction_type'],
                            'expected_account_type' => $expectedType,
                        ],
                    ]);

                    $this->syncDebtStatusIfNeeded($transaction);
                    $created++;
                }

                if ($errors !== []) {
                    throw new \RuntimeException(implode(' | ', $errors));
                }
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['import_file' => $exception->getMessage()])->withInput();
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'entry_bulk_import',
            'description' => "Imported {$created} transactions via spreadsheet.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);

        return redirect()->route('reports.transactions')->with('status', "Transactions imported successfully. Created: {$created}.");
    }

    public function template(Request $request): StreamedResponse
    {
        $format = strtolower((string) $request->query('format', 'csv'));
        $headers = ['transaction_type', 'amount', 'date', 'account_code', 'supplier_name', 'payment_status', 'description'];
        $sampleRows = [
            ['money_in', '2500.00', now()->toDateString(), '4100', '', 'paid', 'Bulk sale posting'],
            ['money_out_general', '400.00', now()->toDateString(), '6200', '', 'pending', 'Marketing expense'],
            ['debts', '1200.00', now()->toDateString(), '2100', 'Acme Supplies Ltd', 'pending', 'Supplier invoice'],
        ];

        if ($format === 'xlsx') {
            return response()->streamDownload(function () use ($headers, $sampleRows): void {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray($headers, null, 'A1');
                $sheet->fromArray($sampleRows, null, 'A2');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 'transactions-import-template.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return response()->streamDownload(function () use ($headers, $sampleRows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'transactions-import-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function authorizeTransaction(Transaction $transaction): void
    {
        if (! auth()->user()->isAdmin() && (int) $transaction->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }

    private function accountsQuery()
    {
        return Account::query()
            ->where('is_active', true)
            ->whereIn('type', ['income', 'cogs', 'expense', 'asset', 'liability'])
            ->orderBy('code');
    }

    private function suppliersQuery()
    {
        return Supplier::query()
            ->where('is_active', true)
            ->orderBy('name');
    }

    private function transactionTypeOptions(): array
    {
        return [
            ['value' => 'money_in', 'label' => 'Money In (Income)'],
            ['value' => 'money_out_direct', 'label' => 'Money Out (Direct/COGS)'],
            ['value' => 'money_out_general', 'label' => 'Money Out (General/Expense)'],
            ['value' => 'valuables', 'label' => 'Valuables (Asset)'],
            ['value' => 'debts', 'label' => 'Debts (Liability)'],
        ];
    }

    private function transactionTypeLabel(?string $transactionType, ?string $accountType): string
    {
        $fromTransactionType = [
            'money_in' => 'Money In',
            'money_out_direct' => 'Money Out (Direct)',
            'money_out_general' => 'Money Out (General)',
            'valuables' => 'Valuables',
            'debts' => 'Debts',
        ];

        if ($transactionType && isset($fromTransactionType[$transactionType])) {
            return $fromTransactionType[$transactionType];
        }

        return match ($accountType) {
            'income' => 'Money In',
            'cogs' => 'Money Out (Direct)',
            'expense' => 'Money Out (General)',
            'asset' => 'Valuables',
            'liability' => 'Debts',
            default => 'Entry',
        };
    }

    private function formatAmount(float $amount): string
    {
        $formatted = number_format($amount, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return 'K'.$formatted;
    }

    private function resolveStoredPaymentStatus(string $transactionType, ?Transaction $transaction, string $requestedStatus): string
    {
        if ($transaction?->parent_transaction_id) {
            return Transaction::PAYMENT_STATUS_PAID;
        }

        if ($transactionType === 'debts') {
            return $transaction?->payment_status ?? Transaction::PAYMENT_STATUS_PENDING;
        }

        return $requestedStatus;
    }

    private function syncDebtStatusIfNeeded(Transaction $transaction): void
    {
        $transaction->loadMissing('account');

        if ($transaction->account?->type === 'liability' && ! $transaction->parent_transaction_id) {
            $transaction->syncPaymentStatus();
        }
    }

    private function normalizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : strtolower($trimmed);
    }

    private function normalizeDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $trimmed = trim($value);

        if (is_numeric($trimmed)) {
            return SpreadsheetDate::excelToDateTimeObject((float) $trimmed)->format('Y-m-d');
        }

        return $trimmed;
    }
}
