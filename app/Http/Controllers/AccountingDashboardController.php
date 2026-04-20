<?php

namespace App\Http\Controllers;

use App\Services\LedgerSummaryService;
use App\Models\Transaction;

class AccountingDashboardController extends Controller
{
    public function index(LedgerSummaryService $ledgerSummaryService)
    {
        $from = now()->startOfMonth()->toDateString();
        $to = now()->endOfMonth()->toDateString();
        $userId = auth()->id();

        $dashboardTotals = $ledgerSummaryService->dashboardTotals($from, $to, $userId);
        $monthlyIncome = $dashboardTotals['income'];
        $monthlyDirectCosts = $dashboardTotals['directCosts'];
        $monthlyOperatingProfit = $dashboardTotals['operatingProfit'];

        $recentTransactions = Transaction::with('account')
            ->withSum('paymentTransactions as paid_amount', 'amount')
            ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
            ->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId])
            ->select('transactions.*')
            ->latest('transactions.date')
            ->latest('transactions.id')
            ->limit(100)
            ->get();

        return view('accounting.dashboard', compact(
            'monthlyIncome',
            'monthlyDirectCosts',
            'monthlyOperatingProfit',
            'recentTransactions',
            'from',
            'to'
        ));
    }
}