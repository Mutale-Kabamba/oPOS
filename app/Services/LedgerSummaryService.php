<?php

namespace App\Services;

use App\Models\JournalLine;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LedgerSummaryService
{
    public function incomeStatement(string $from, string $to, ?int $userId = null): array
    {
        $baseQuery = $this->baseJournalQuery($userId)
            ->whereDate('journal_entries.entry_date', '>=', $from)
            ->whereDate('journal_entries.entry_date', '<=', $to);

        $totals = (clone $baseQuery)
            ->selectRaw("SUM(CASE WHEN accounts.type = 'income' THEN journal_lines.credit - journal_lines.debit ELSE 0 END) as total_income")
            ->selectRaw("SUM(CASE WHEN accounts.type = 'cogs' THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as direct_costs")
            ->selectRaw("SUM(CASE WHEN accounts.type = 'expense' THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as general_expenses")
            ->first();

        $totalIncome = round((float) ($totals->total_income ?? 0), 2);
        $directCosts = round((float) ($totals->direct_costs ?? 0), 2);
        $generalExpenses = round((float) ($totals->general_expenses ?? 0), 2);
        $grossProfit = round($totalIncome - $directCosts, 2);
        $netProfit = round($grossProfit - $generalExpenses, 2);

        $monthlyRows = (clone $baseQuery)
            ->selectRaw($this->monthlyDateExpression('journal_entries.entry_date').' as month')
            ->selectRaw("SUM(CASE WHEN accounts.type = 'income' THEN journal_lines.credit - journal_lines.debit ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN accounts.type = 'cogs' THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as cogs")
            ->selectRaw("SUM(CASE WHEN accounts.type = 'expense' THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as expenses")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                $income = round((float) $row->income, 2);
                $cogs = round((float) $row->cogs, 2);
                $expenses = round((float) $row->expenses, 2);
                $gross = round($income - $cogs, 2);

                return [
                    'month' => $row->month,
                    'income' => $income,
                    'cogs' => $cogs,
                    'gross' => $gross,
                    'expenses' => $expenses,
                    'net' => round($gross - $expenses, 2),
                ];
            })
            ->values();

        return compact(
            'totalIncome',
            'directCosts',
            'generalExpenses',
            'grossProfit',
            'netProfit',
            'monthlyRows'
        );
    }

    public function balanceSheet(string $asOf, ?int $userId = null): array
    {
        $baseQuery = $this->baseJournalQuery($userId)
            ->whereDate('journal_entries.entry_date', '<=', $asOf);

        $totals = (clone $baseQuery)
            ->selectRaw("SUM(CASE WHEN accounts.type = 'asset' THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as total_assets")
            ->selectRaw("SUM(CASE WHEN accounts.type = 'liability' THEN journal_lines.credit - journal_lines.debit ELSE 0 END) as total_liabilities")
            ->first();

        $totalValuables = round((float) ($totals->total_assets ?? 0), 2);
        $totalDebts = round((float) ($totals->total_liabilities ?? 0), 2);
        $equity = round($totalValuables - $totalDebts, 2);
        $equationGap = round($totalValuables - ($totalDebts + $equity), 2);

        return compact('totalValuables', 'totalDebts', 'equity', 'equationGap');
    }

    public function dashboardTotals(string $from, string $to, ?int $userId = null): array
    {
        $summary = $this->incomeStatement($from, $to, $userId);

        return [
            'income' => $summary['totalIncome'],
            'directCosts' => $summary['directCosts'],
            'operatingProfit' => $summary['netProfit'],
        ];
    }

    public function salesSummary(string $from, string $to, ?int $userId = null): array
    {
        $baseQuery = $this->baseJournalQuery($userId)
            ->whereDate('journal_entries.entry_date', '>=', $from)
            ->whereDate('journal_entries.entry_date', '<=', $to)
            ->where('accounts.type', 'income');

        $totalSales = round((float) (clone $baseQuery)
            ->selectRaw('SUM(journal_lines.credit - journal_lines.debit) as total_sales')
            ->value('total_sales'), 2);

        $dailyRows = (clone $baseQuery)
            ->selectRaw('DATE(journal_entries.entry_date) as period')
            ->selectRaw('COUNT(DISTINCT journal_entries.transaction_id) as transactions_count')
            ->selectRaw('SUM(journal_lines.credit - journal_lines.debit) as total_amount')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => (object) [
                'period' => $row->period,
                'transactions_count' => (int) $row->transactions_count,
                'total_amount' => round((float) $row->total_amount, 2),
            ]);

        $monthlyRows = (clone $baseQuery)
            ->selectRaw($this->monthlyDateExpression('journal_entries.entry_date').' as period')
            ->selectRaw('COUNT(DISTINCT journal_entries.transaction_id) as transactions_count')
            ->selectRaw('SUM(journal_lines.credit - journal_lines.debit) as total_amount')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => (object) [
                'period' => $row->period,
                'transactions_count' => (int) $row->transactions_count,
                'total_amount' => round((float) $row->total_amount, 2),
            ]);

        $categoryExpression = "COALESCE(categories.name, 'Uncategorized')";

        $categoryRows = (clone $baseQuery)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("{$categoryExpression} as category_name")
            ->selectRaw('COUNT(DISTINCT journal_entries.transaction_id) as transactions_count')
            ->selectRaw('SUM(journal_lines.credit - journal_lines.debit) as total_amount')
            ->groupBy(DB::raw($categoryExpression))
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => (object) [
                'category_name' => $row->category_name,
                'transactions_count' => (int) $row->transactions_count,
                'total_amount' => round((float) $row->total_amount, 2),
            ]);

        $userRows = (clone $baseQuery)
            ->leftJoin('users as owners', function ($join) {
                $join->whereRaw('owners.id = COALESCE(parent_transactions.user_id, transactions.user_id)');
            })
            ->select('owners.name')
            ->selectRaw('COUNT(DISTINCT journal_entries.transaction_id) as transactions_count')
            ->selectRaw('SUM(journal_lines.credit - journal_lines.debit) as total_amount')
            ->groupBy('owners.id', 'owners.name')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => (object) [
                'name' => $row->name,
                'transactions_count' => (int) $row->transactions_count,
                'total_amount' => round((float) $row->total_amount, 2),
            ]);

        return compact('totalSales', 'dailyRows', 'monthlyRows', 'categoryRows', 'userRows');
    }

    public function reconciliationSummary(int $accountId, string $asOf, ?int $userId = null): array
    {
        $unclearedRows = Transaction::query()
            ->join('journal_entries', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->join('journal_lines', function ($join) use ($accountId) {
                $join->on('journal_lines.journal_entry_id', '=', 'journal_entries.id')
                    ->where('journal_lines.account_id', '=', $accountId);
            })
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->where('transactions.is_reconciled', false)
            ->when($userId !== null, fn ($query) => $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId]))
            ->with(['supplier', 'user'])
            ->select('transactions.*')
            ->selectRaw('SUM(journal_lines.debit - journal_lines.credit) as movement_amount')
            ->groupBy('transactions.id')
            ->orderBy('transactions.date')
            ->orderBy('transactions.id')
            ->get()
            ->map(function (Transaction $transaction) {
                $transaction->movement_amount = round((float) $transaction->movement_amount, 2);

                return $transaction;
            });

        $clearedBalance = round((float) JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->where('journal_lines.account_id', $accountId)
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->where('transactions.is_reconciled', true)
            ->when($userId !== null, fn ($query) => $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId]))
            ->sum(DB::raw('journal_lines.debit - journal_lines.credit')), 2);

        $unclearedBalance = round((float) $unclearedRows->sum('movement_amount'), 2);

        return compact('unclearedRows', 'clearedBalance', 'unclearedBalance');
    }

    public function assetAccountsForReconciliation(?int $userId = null): Collection
    {
        return DB::table('accounts')
            ->join('journal_lines', 'journal_lines.account_id', '=', 'accounts.id')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->where('accounts.is_active', true)
            ->where('accounts.type', 'asset')
            ->when($userId !== null, fn ($query) => $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId]))
            ->select('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type', 'accounts.group_name', 'accounts.is_active')
            ->distinct()
            ->orderBy('accounts.code')
            ->get();
    }

    private function baseJournalQuery(?int $userId = null): Builder
    {
        $query = JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->join('accounts', 'accounts.id', '=', 'journal_lines.account_id');

        if ($userId !== null) {
            $query->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId]);
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