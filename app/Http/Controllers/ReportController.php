<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\LedgerSummaryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }

    public function transactions(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());
        $q = trim((string) $request->input('q', ''));

        $rows = $this->buildTransactionsQuery($request, $from, $to, $q)
            ->paginate(15)
            ->withQueryString();

        return view('reports.transactions', compact('rows', 'from', 'to', 'q'));
    }

    public function transactionsPdf(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());
        $q = trim((string) $request->input('q', ''));
        $reportType = (string) $request->input('report_type', 'full');

        if ($reportType === 'combined') {
            $fullRows = $this->buildTransactionsQuery($request, $from, $to, $q)->get();
            $incomeRows = $this->buildTransactionsQuery($request, $from, $to, $q, ['income'])->get();
            $expenseRows = $this->buildTransactionsQuery($request, $from, $to, $q, ['expense', 'cogs'])->get();

            return Pdf::loadView('reports.transactions_combined_pdf', compact('from', 'to', 'q', 'fullRows', 'incomeRows', 'expenseRows'))
                ->download("transactions-combined-{$from}-to-{$to}.pdf");
        }

        $accountTypes = match ($reportType) {
            'income' => ['income'],
            'expense' => ['expense', 'cogs'],
            default => null,
        };

        $rows = $this->buildTransactionsQuery($request, $from, $to, $q, $accountTypes)->get();
        $reportTypeLabel = match ($reportType) {
            'income' => 'Income Report',
            'expense' => 'Expenses Report',
            default => 'Full Report',
        };

        return Pdf::loadView('reports.transactions_pdf', compact('rows', 'from', 'to', 'q', 'reportTypeLabel'))
            ->download("transactions-{$reportType}-{$from}-to-{$to}.pdf");
    }

    private function buildTransactionsQuery(Request $request, string $from, string $to, string $q, ?array $accountTypes = null)
    {
        $query = Transaction::with(['account', 'user'])
            ->withSum('paymentTransactions as paid_amount', 'amount')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->whereBetween('transactions.date', [$from, $to])
            ->select('transactions.*')
            ->orderByDesc('transactions.date');

        if (! $request->user()->isAdmin()) {
            $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$request->user()->id]);
        }

        if ($accountTypes !== null) {
            $query->whereHas('account', function ($accountQuery) use ($accountTypes) {
                $accountQuery->whereIn('type', $accountTypes);
            });
        }

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('transactions.description', 'like', "%{$q}%")
                    ->orWhere('transactions.amount', 'like', "%{$q}%")
                    ->orWhereHas('account', function ($accountQuery) use ($q) {
                        $accountQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('code', 'like', "%{$q}%")
                            ->orWhere('type', 'like', "%{$q}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        return $query;
    }

    public function incomeStatement(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $summary = $ledgerSummaryService->incomeStatement(
            $from,
            $to,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return view('reports.income_statement', compact(
            'from',
            'to',
            'totalIncome',
            'directCosts',
            'grossProfit',
            'generalExpenses',
            'netProfit',
            'monthlyRows'
        ));
    }

    public function incomeStatementPdf(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $summary = $ledgerSummaryService->incomeStatement(
            $from,
            $to,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return Pdf::loadView('reports.income_statement_pdf', compact(
            'from',
            'to',
            'totalIncome',
            'directCosts',
            'grossProfit',
            'generalExpenses',
            'netProfit'
        ))->download("monthly-statement-{$from}-to-{$to}.pdf");
    }

    public function balanceSheet(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $asOf = $request->input('as_of', now()->toDateString());

        $summary = $ledgerSummaryService->balanceSheet(
            $asOf,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return view('reports.balance_sheet', compact('asOf', 'totalValuables', 'totalDebts', 'equity', 'equationGap'));
    }

    public function balanceSheetPdf(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $asOf = $request->input('as_of', now()->toDateString());

        $summary = $ledgerSummaryService->balanceSheet(
            $asOf,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return Pdf::loadView('reports.balance_sheet_pdf', compact('asOf', 'totalValuables', 'totalDebts', 'equity', 'equationGap'))
            ->download("balance-sheet-{$asOf}.pdf");
    }

    public function trialBalance(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());
        $rows = $this->trialBalanceRows($request, $from, $to);
        $totalDebits = round((float) $rows->sum('debit'), 2);
        $totalCredits = round((float) $rows->sum('credit'), 2);
        $difference = round($totalDebits - $totalCredits, 2);

        return view('reports.trial_balance', compact('from', 'to', 'rows', 'totalDebits', 'totalCredits', 'difference'));
    }

    public function trialBalancePdf(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());
        $rows = $this->trialBalanceRows($request, $from, $to);
        $totalDebits = round((float) $rows->sum('debit'), 2);
        $totalCredits = round((float) $rows->sum('credit'), 2);
        $difference = round($totalDebits - $totalCredits, 2);

        return Pdf::loadView('reports.trial_balance_pdf', compact('from', 'to', 'rows', 'totalDebits', 'totalCredits', 'difference'))
            ->download("trial-balance-{$from}-to-{$to}.pdf");
    }

    public function suppliersAging(Request $request)
    {
        $asOf = $request->input('as_of', now()->toDateString());
        $debts = $this->supplierOutstandingDebtRows($request, $asOf);
        $rows = $this->supplierAgingRows($request, $asOf);
        $totals = $this->supplierAgingTotals($rows);

        return view('reports.suppliers_aging', compact('asOf', 'rows', 'totals', 'debts'));
    }

    public function suppliersAgingPdf(Request $request)
    {
        $asOf = $request->input('as_of', now()->toDateString());
        $debts = $this->supplierOutstandingDebtRows($request, $asOf);
        $rows = $this->supplierAgingRows($request, $asOf);
        $totals = $this->supplierAgingTotals($rows);

        return Pdf::loadView('reports.suppliers_aging_pdf', compact('asOf', 'rows', 'totals', 'debts'))
            ->download("suppliers-aging-{$asOf}.pdf");
    }

    public function recordSupplierPayment(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeSupplierDebt($request, $transaction);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'as_of' => ['nullable', 'date'],
        ]);

        $transaction->loadMissing('account', 'supplier');

        $remainingAmount = $transaction->remainingAmount();
        $paymentAmount = round((float) $validated['amount'], 2);
        $asOf = $validated['as_of'] ?? now()->toDateString();

        if ($remainingAmount <= 0) {
            return redirect()->route('reports.suppliers-aging', ['as_of' => $asOf])
                ->withErrors(['amount' => 'This debt has already been fully paid.']);
        }

        if ($paymentAmount > $remainingAmount) {
            return redirect()->route('reports.suppliers-aging', ['as_of' => $asOf])
                ->withErrors(['amount' => 'Payment amount cannot exceed the remaining balance.']);
        }

        Transaction::create([
            'amount' => $paymentAmount,
            'date' => $validated['date'],
            'account_id' => $transaction->account_id,
            'supplier_id' => $transaction->supplier_id,
            'parent_transaction_id' => $transaction->id,
            'category_id' => $transaction->category_id,
            'description' => $validated['description'] ?: 'Debt payment for supplier liability #'.$transaction->id,
            'payment_status' => Transaction::PAYMENT_STATUS_PAID,
            'user_id' => $transaction->user_id,
            'metadata' => [
                'source' => 'supplier-aging-payment',
                'transaction_type' => 'debt_payment',
                'expected_account_type' => 'liability',
                'parent_transaction_id' => $transaction->id,
                'recorded_by_user_id' => $request->user()->id,
            ],
        ]);

        $transaction->syncPaymentStatus();

        return redirect()->route('reports.suppliers-aging', ['as_of' => $asOf])
            ->with('status', 'Supplier payment recorded successfully.');
    }

    public function sales(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $summary = $ledgerSummaryService->salesSummary(
            $from,
            $to,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return view('reports.sales', compact('from', 'to', 'totalSales', 'dailyRows', 'monthlyRows', 'categoryRows', 'userRows'));
    }

    public function salesPdf(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        $summary = $ledgerSummaryService->salesSummary(
            $from,
            $to,
            $request->user()->isAdmin() ? null : $request->user()->id,
        );

        extract($summary);

        return Pdf::loadView('reports.sales_pdf', compact('from', 'to', 'totalSales', 'dailyRows', 'monthlyRows', 'categoryRows', 'userRows'))
            ->download("sales-report-{$from}-to-{$to}.pdf");
    }

    public function reconciliation(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $accounts = $ledgerSummaryService->assetAccountsForReconciliation(
            $request->user()->isAdmin() ? null : $request->user()->id,
        );
        $asOf = $request->input('as_of', now()->toDateString());
        $selectedAccountId = $request->integer('account_id');

        if (! $selectedAccountId && $accounts->isNotEmpty()) {
            $selectedAccountId = (int) $accounts->first()->id;
        }

        $selectedAccount = $selectedAccountId
            ? $accounts->firstWhere('id', $selectedAccountId)
            : null;

        abort_if($selectedAccountId && ! $selectedAccount, 404);

        $statementEndingBalance = $request->filled('statement_ending_balance')
            ? round((float) $request->input('statement_ending_balance'), 2)
            : null;

        $unclearedRows = collect();
        $clearedBalance = 0.0;
        $unclearedBalance = 0.0;

        if ($selectedAccount) {
            $summary = $ledgerSummaryService->reconciliationSummary(
                (int) $selectedAccount->id,
                $asOf,
                $request->user()->isAdmin() ? null : $request->user()->id,
            );

            $unclearedRows = $summary['unclearedRows'];
            $clearedBalance = $summary['clearedBalance'];
            $unclearedBalance = $summary['unclearedBalance'];
        }

        $variance = $statementEndingBalance !== null
            ? round($clearedBalance - $statementEndingBalance, 2)
            : null;

        return view('reports.reconciliation', compact(
            'accounts',
            'selectedAccount',
            'asOf',
            'statementEndingBalance',
            'unclearedRows',
            'clearedBalance',
            'unclearedBalance',
            'variance'
        ));
    }

    public function reconcileTransactions(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'as_of' => ['required', 'date'],
            'statement_ending_balance' => ['nullable', 'numeric'],
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['required', 'integer'],
        ]);

        $account = Account::query()->findOrFail((int) $validated['account_id']);

        $transactionIds = Transaction::query()
            ->join('journal_entries', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->join('journal_lines', function ($join) use ($validated) {
                $join->on('journal_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_lines.account_id', '=', (int) $validated['account_id']);
            })
            ->whereDate('journal_entries.entry_date', '<=', (string) $validated['as_of'])
            ->where('transactions.is_reconciled', false)
            ->whereIn('transactions.id', $validated['transaction_ids'])
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$request->user()->id]))
            ->pluck('transactions.id');

        if ($transactionIds->isEmpty()) {
            return redirect()->route('reports.reconciliation', [
                'account_id' => $account->id,
                'as_of' => $validated['as_of'],
                'statement_ending_balance' => $validated['statement_ending_balance'] ?? null,
            ])->with('status', 'No eligible transactions were selected for reconciliation.');
        }

        Transaction::query()
            ->whereIn('id', $transactionIds)
            ->update([
                'is_reconciled' => true,
                'reconciled_at' => now(),
            ]);

        return redirect()->route('reports.reconciliation', [
            'account_id' => $account->id,
            'as_of' => $validated['as_of'],
            'statement_ending_balance' => $validated['statement_ending_balance'] ?? null,
        ])->with('status', $transactionIds->count().' transaction(s) marked as reconciled.');
    }

    public function reconciliationPdf(Request $request, LedgerSummaryService $ledgerSummaryService)
    {
        $accounts = $ledgerSummaryService->assetAccountsForReconciliation(
            $request->user()->isAdmin() ? null : $request->user()->id,
        );
        $asOf = $request->input('as_of', now()->toDateString());
        $selectedAccountId = $request->integer('account_id');

        if (! $selectedAccountId && $accounts->isNotEmpty()) {
            $selectedAccountId = (int) $accounts->first()->id;
        }

        $selectedAccount = $selectedAccountId
            ? $accounts->firstWhere('id', $selectedAccountId)
            : null;

        abort_if($selectedAccountId && ! $selectedAccount, 404);

        $statementEndingBalance = $request->filled('statement_ending_balance')
            ? round((float) $request->input('statement_ending_balance'), 2)
            : null;

        $summary = $selectedAccount
            ? $ledgerSummaryService->reconciliationSummary(
                (int) $selectedAccount->id,
                $asOf,
                $request->user()->isAdmin() ? null : $request->user()->id,
            )
            : ['unclearedRows' => collect(), 'clearedBalance' => 0.0, 'unclearedBalance' => 0.0];

        $unclearedRows = $summary['unclearedRows'];
        $clearedBalance = $summary['clearedBalance'];
        $unclearedBalance = $summary['unclearedBalance'];
        $variance = $statementEndingBalance !== null
            ? round($clearedBalance - $statementEndingBalance, 2)
            : null;

        return Pdf::loadView('reports.reconciliation_pdf', compact(
            'selectedAccount',
            'asOf',
            'statementEndingBalance',
            'unclearedRows',
            'clearedBalance',
            'unclearedBalance',
            'variance'
        ))->download("reconciliation-{$asOf}.pdf");
    }

    private function trialBalanceRows(Request $request, string $from, string $to): Collection
    {
        return Account::query()
            ->leftJoin('journal_lines', 'journal_lines.account_id', '=', 'accounts.id')
            ->leftJoin('journal_entries', function ($join) use ($from, $to, $request) {
                $join->on('journal_entries.id', '=', 'journal_lines.journal_entry_id')
                    ->whereDate('journal_entries.entry_date', '>=', $from)
                    ->whereDate('journal_entries.entry_date', '<=', $to);
            })
            ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$request->user()->id]))
            ->where('accounts.is_active', true)
            ->select('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type', 'accounts.group_name')
            ->selectRaw('COALESCE(SUM(CASE WHEN journal_entries.id IS NOT NULL THEN journal_lines.debit ELSE 0 END), 0) as debit_total')
            ->selectRaw('COALESCE(SUM(CASE WHEN journal_entries.id IS NOT NULL THEN journal_lines.credit ELSE 0 END), 0) as credit_total')
            ->selectRaw('COALESCE(SUM(CASE WHEN journal_entries.id IS NOT NULL THEN journal_lines.debit - journal_lines.credit ELSE 0 END), 0) as net_balance')
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type', 'accounts.group_name')
            ->orderBy('accounts.code')
            ->get()
            ->map(function ($row) {
                $debit = round((float) $row->debit_total, 2);
                $credit = round((float) $row->credit_total, 2);
                $closingBalance = round(abs((float) $row->net_balance), 2);

                return (object) [
                    'code' => $row->code,
                    'name' => $row->name,
                    'type' => $row->type,
                    'closing_balance' => $closingBalance,
                    'debit' => $debit,
                    'credit' => $credit,
                ];
            });
    }

    private function supplierAgingRows(Request $request, string $asOf): Collection
    {
        $asOfDate = Carbon::parse($asOf)->startOfDay();

        return $this->supplierOutstandingDebtRows($request, $asOf)
            ->groupBy('supplier_id')
            ->map(function (Collection $group) use ($asOfDate) {
                $supplier = $group->first()->supplier;
                $buckets = [
                    'current' => 0.0,
                    'days_1_30' => 0.0,
                    'days_31_60' => 0.0,
                    'days_61_90' => 0.0,
                    'days_90_plus' => 0.0,
                ];

                foreach ($group as $transaction) {
                    $daysOutstanding = Carbon::parse($transaction->date)->startOfDay()->diffInDays($asOfDate);
                    $amount = round((float) $transaction->remaining_amount, 2);

                    if ($daysOutstanding === 0) {
                        $buckets['current'] += $amount;
                    } elseif ($daysOutstanding <= 30) {
                        $buckets['days_1_30'] += $amount;
                    } elseif ($daysOutstanding <= 60) {
                        $buckets['days_31_60'] += $amount;
                    } elseif ($daysOutstanding <= 90) {
                        $buckets['days_61_90'] += $amount;
                    } else {
                        $buckets['days_90_plus'] += $amount;
                    }
                }

                $totalDue = round(array_sum($buckets), 2);

                return (object) array_merge([
                    'supplier_name' => $supplier?->name ?? 'Unknown Supplier',
                    'contact_person' => $supplier?->contact_person,
                    'total_due' => $totalDue,
                ], array_map(fn ($value) => round($value, 2), $buckets));
            })
            ->sortBy('supplier_name')
            ->values();
    }

    private function supplierOutstandingDebtRows(Request $request, string $asOf): Collection
    {
        $asOfDate = Carbon::parse($asOf)->startOfDay();

        return $this->supplierAgingBaseQuery($request, $asOf)
            ->with(['supplier', 'account'])
            ->withSum('paymentTransactions as paid_amount', 'amount')
            ->get()
            ->map(function (Transaction $transaction) use ($asOfDate) {
                $paidAmount = round((float) ($transaction->paid_amount ?? 0), 2);
                $remainingAmount = round(max((float) $transaction->amount - $paidAmount, 0), 2);

                return (object) [
                    'id' => $transaction->id,
                    'supplier_id' => $transaction->supplier_id,
                    'supplier' => $transaction->supplier,
                    'account_name' => $transaction->account?->name,
                    'description' => $transaction->description,
                    'date' => $transaction->date,
                    'original_amount' => round((float) $transaction->amount, 2),
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'payment_status' => $transaction->payment_status,
                    'days_outstanding' => Carbon::parse($transaction->date)->startOfDay()->diffInDays($asOfDate),
                ];
            })
            ->filter(fn (object $transaction) => $transaction->remaining_amount > 0)
            ->sortBy(['supplier.name', 'date'])
            ->values();
    }

    private function supplierAgingTotals(Collection $rows): array
    {
        return [
            'current' => round((float) $rows->sum('current'), 2),
            'days_1_30' => round((float) $rows->sum('days_1_30'), 2),
            'days_31_60' => round((float) $rows->sum('days_31_60'), 2),
            'days_61_90' => round((float) $rows->sum('days_61_90'), 2),
            'days_90_plus' => round((float) $rows->sum('days_90_plus'), 2),
            'total_due' => round((float) $rows->sum('total_due'), 2),
        ];
    }

    private function supplierAgingBaseQuery(Request $request, string $asOf): Builder
    {
        $query = Transaction::query()
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'liability')
            ->whereNull('transactions.parent_transaction_id')
            ->whereIn('transactions.payment_status', [
                Transaction::PAYMENT_STATUS_PENDING,
                Transaction::PAYMENT_STATUS_PARTIALLY_PAID,
            ])
            ->whereNotNull('transactions.supplier_id')
            ->whereDate('transactions.date', '<=', $asOf)
            ->select('transactions.*');

        return $this->applyUserScope($query, $request);
    }

    private function authorizeSupplierDebt(Request $request, Transaction $transaction): void
    {
        abort_if($transaction->parent_transaction_id !== null, 404);

        $transaction->loadMissing('account');

        abort_if($transaction->account?->type !== 'liability' || ! $transaction->supplier_id, 404);

        if (! $request->user()->isAdmin() && (int) $transaction->user_id !== (int) $request->user()->id) {
            abort(403);
        }
    }

    private function applyUserScope(Builder $query, Request $request, string $column = 'transactions.user_id'): Builder
    {
        if (! $request->user()->isAdmin()) {
            $query->where($column, $request->user()->id);
        }

        return $query;
    }

    private function monthlyDateExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
