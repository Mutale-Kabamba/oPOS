<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::query()->orderBy('code')->get();

        return view('admin.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['asset', 'liability', 'income', 'cogs', 'expense'])],
            'group_name' => ['required', Rule::in(['valuables', 'debts', 'money_in', 'direct_costs', 'general_costs'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Account::create($data);

        return redirect()->route('admin.accounts.index')->with('status', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('accounts', 'code')->ignore($account->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['asset', 'liability', 'income', 'cogs', 'expense'])],
            'group_name' => ['required', Rule::in(['valuables', 'debts', 'money_in', 'direct_costs', 'general_costs'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $account->update($data);

        return redirect()->route('admin.accounts.index')->with('status', 'Account updated successfully.');
    }

    public function toggle(Account $account)
    {
        $account->update(['is_active' => ! $account->is_active]);

        return redirect()->route('admin.accounts.index')->with('status', 'Account status updated.');
    }

    public function destroy(Account $account)
    {
        if ($account->transactions()->exists()) {
            return redirect()->route('admin.accounts.index')->with('status', 'Cannot delete account with existing transactions.');
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')->with('status', 'Account deleted successfully.');
    }
}
