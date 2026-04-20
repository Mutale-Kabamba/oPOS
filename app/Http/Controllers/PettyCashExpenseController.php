<?php

namespace App\Http\Controllers;

use App\Models\PettyCashAllocation;
use App\Models\PettyCashExpense;
use Illuminate\Http\Request;

class PettyCashExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month', now()->format('Y-m'));

        $allocation = PettyCashAllocation::where('user_id', $user->id)
            ->where('month', $month)
            ->first();

        $expenses = PettyCashExpense::where('user_id', $user->id)
            ->whereRaw("strftime('%Y-%m', expense_date) = ?", [$month])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $allocated = $allocation?->amount ?? 0;
        $spent = $expenses->sum('amount');
        $balance = $allocated - $spent;

        $dailyBreakdown = $expenses->groupBy(fn ($e) => $e->expense_date->format('Y-m-d'))
            ->map(fn ($group) => (object) [
                'date' => $group->first()->expense_date,
                'total' => $group->sum('amount'),
                'items' => $group,
            ])
            ->sortKeysDesc();

        return view('pos.petty-cash.index', compact(
            'allocation', 'expenses', 'allocated', 'spent', 'balance', 'month', 'dailyBreakdown'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
        ]);

        $user = auth()->user();
        $month = date('Y-m', strtotime($validated['expense_date']));

        $allocation = PettyCashAllocation::where('user_id', $user->id)
            ->where('month', $month)
            ->first();

        if (! $allocation) {
            return back()->withErrors(['amount' => 'No petty cash allocated for this month.'])->withInput();
        }

        $spent = PettyCashExpense::where('user_id', $user->id)
            ->whereRaw("strftime('%Y-%m', expense_date) = ?", [$month])
            ->sum('amount');

        if (($spent + $validated['amount']) > $allocation->amount) {
            return back()->withErrors(['amount' => 'This expense exceeds your remaining petty cash balance of K ' . number_format($allocation->amount - $spent, 2) . '.'])->withInput();
        }

        PettyCashExpense::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'expense_date' => $validated['expense_date'],
        ]);

        return redirect()->route('pos.petty-cash.index', ['month' => $month])
            ->with('status', 'Expense recorded successfully.');
    }

    public function destroy(PettyCashExpense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $month = $expense->expense_date->format('Y-m');
        $expense->delete();

        return redirect()->route('pos.petty-cash.index', ['month' => $month])
            ->with('status', 'Expense deleted.');
    }
}
