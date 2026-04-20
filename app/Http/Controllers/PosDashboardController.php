<?php

namespace App\Http\Controllers;

use App\Models\PosSale;
use App\Models\PosProduct;
use App\Models\PettyCashAllocation;
use App\Models\PettyCashExpense;

class PosDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $todaySales = PosSale::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        $todayRevenue = PosSale::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total');

        $monthSales = PosSale::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $monthRevenue = PosSale::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $recentSales = PosSale::where('user_id', $user->id)
            ->with('items.product')
            ->latest()
            ->limit(10)
            ->get();

        $lowStockProducts = PosProduct::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $currentMonth = now()->format('Y-m');
        $pettyCashAllocation = PettyCashAllocation::where('user_id', $user->id)
            ->where('month', $currentMonth)
            ->first();
        $pettyCashAllocated = $pettyCashAllocation?->amount ?? 0;
        $pettyCashSpent = PettyCashExpense::where('user_id', $user->id)
            ->whereRaw("strftime('%Y-%m', expense_date) = ?", [$currentMonth])
            ->sum('amount');
        $pettyCashBalance = $pettyCashAllocated - $pettyCashSpent;

        return view('pos.dashboard', compact(
            'todaySales',
            'todayRevenue',
            'monthSales',
            'monthRevenue',
            'recentSales',
            'lowStockProducts',
            'pettyCashAllocated',
            'pettyCashSpent',
            'pettyCashBalance'
        ));
    }
}
