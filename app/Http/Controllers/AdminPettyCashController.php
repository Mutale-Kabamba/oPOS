<?php

namespace App\Http\Controllers;

use App\Models\PettyCashAllocation;
use App\Models\PettyCashExpense;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPettyCashController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $salespersons = User::where('role', 'salesperson')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $allocations = PettyCashAllocation::where('month', $month)
            ->with('user', 'allocator')
            ->get()
            ->keyBy('user_id');

        $expenses = PettyCashExpense::whereIn('user_id', $salespersons->pluck('id'))
            ->whereRaw("strftime('%Y-%m', expense_date) = ?", [$month])
            ->get()
            ->groupBy('user_id');

        $summary = $salespersons->map(function ($sp) use ($allocations, $expenses, $month) {
            $allocation = $allocations->get($sp->id);
            $allocated = $allocation?->amount ?? 0;
            $userExpenses = $expenses->get($sp->id, collect());
            $spent = $userExpenses->sum('amount');

            return (object) [
                'user' => $sp,
                'allocated' => $allocated,
                'spent' => $spent,
                'balance' => $allocated - $spent,
                'allocation' => $allocation,
                'expense_count' => $userExpenses->count(),
            ];
        });

        return view('admin.petty-cash.index', compact('summary', 'month', 'salespersons'));
    }

    public function allocate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|date_format:Y-m',
            'note' => 'nullable|string|max:500',
        ]);

        PettyCashAllocation::updateOrCreate(
            ['user_id' => $validated['user_id'], 'month' => $validated['month']],
            [
                'amount' => $validated['amount'],
                'allocated_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
            ]
        );

        return redirect()->route('admin.petty-cash.index', ['month' => $validated['month']])
            ->with('status', 'Petty cash allocated successfully.');
    }

    public function report(Request $request, User $user)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $allocation = PettyCashAllocation::where('user_id', $user->id)
            ->where('month', $month)
            ->first();

        $expenses = PettyCashExpense::where('user_id', $user->id)
            ->whereRaw("strftime('%Y-%m', expense_date) = ?", [$month])
            ->orderBy('expense_date', 'desc')
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

        return view('admin.petty-cash.report', compact(
            'user', 'month', 'allocation', 'expenses', 'allocated', 'spent', 'balance', 'dailyBreakdown'
        ));
    }
}
