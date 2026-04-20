<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Account;
use App\Services\PostingRuleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $activity = AuditLog::with('user')
            ->latest('occurred_at')
            ->limit(200)
            ->get();

        return view('admin.settings.index', compact('activity'));
    }

    public function accountantSettings(PostingRuleService $postingRuleService)
    {
        $activity = AuditLog::with('user')
            ->latest('occurred_at')
            ->limit(200)
            ->get();

        $postingRuleAccounts = Account::query()
            ->where('is_active', true)
            ->where('type', 'asset')
            ->orderBy('code')
            ->get();

        $postingRules = $postingRuleService->rules();

        return view('accounting.settings', compact('activity', 'postingRuleAccounts', 'postingRules'));
    }

    public function updatePostingRules(Request $request, PostingRuleService $postingRuleService)
    {
        $data = $request->validate([
            'settlement_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'contra_account_id' => ['required', 'integer', 'exists:accounts,id', 'different:settlement_account_id'],
        ]);

        $accounts = Account::query()
            ->whereIn('id', [$data['settlement_account_id'], $data['contra_account_id']])
            ->where('type', 'asset')
            ->pluck('id');

        if ($accounts->count() !== 2) {
            return back()->withErrors([
                'settlement_account_id' => 'Both posting rule accounts must be active asset accounts.',
            ])->withInput();
        }

        $postingRuleService->update((int) $data['settlement_account_id'], (int) $data['contra_account_id']);

        return redirect()->route('accounting.settings')->with('status', 'Posting rules updated successfully.');
    }

    public function activityPdf()
    {
        $activity = AuditLog::with('user')
            ->latest('occurred_at')
            ->get();

        return Pdf::loadView('reports.activity_pdf', compact('activity'))
            ->download('user-activity-report.pdf');
    }
}
