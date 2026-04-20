<?php

namespace App\Http\Controllers;

use App\Models\PosProduct;
use App\Models\PosSale;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = PosProduct::where('is_active', true)->count();
        $lowStockCount = PosProduct::where('is_active', true)->where('stock', '<=', 5)->count();

        $todaySales = PosSale::whereDate('created_at', today())->count();
        $todayRevenue = PosSale::whereDate('created_at', today())->sum('total');

        $monthSales = PosSale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthRevenue = PosSale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $lowStockProducts = PosProduct::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        $recentSales = PosSale::with(['user', 'items.product'])
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'lowStockCount',
            'todaySales',
            'todayRevenue',
            'monthSales',
            'monthRevenue',
            'lowStockProducts',
            'recentSales'
        ));
    }
}
