<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PostingRuleService;
use Illuminate\Support\Facades\DB;

$accountant = User::factory()->create([
    'role' => 'accountant',
    'is_active' => true,
]);

$admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
]);

$settlement = Account::query()->updateOrCreate(
    ['code' => '1100'],
    ['name' => 'Cash', 'type' => 'asset', 'group_name' => 'valuables', 'is_active' => true]
);
$contra = Account::query()->updateOrCreate(
    ['code' => '1199'],
    ['name' => 'Clearing', 'type' => 'asset', 'group_name' => 'valuables', 'is_active' => true]
);
$liability = Account::query()->updateOrCreate(
    ['code' => '2100'],
    ['name' => 'Supplier Bills', 'type' => 'liability', 'group_name' => 'debts', 'is_active' => true]
);

app(PostingRuleService::class)->update($settlement->id, $contra->id);

$debt = Transaction::create([
    'amount' => 5000,
    'date' => now()->subDays(2)->toDateString(),
    'account_id' => $liability->id,
    'payment_status' => Transaction::PAYMENT_STATUS_PENDING,
    'user_id' => $accountant->id,
    'metadata' => ['transaction_type' => 'debts'],
]);

$payment = Transaction::create([
    'amount' => 2500,
    'date' => now()->toDateString(),
    'account_id' => $liability->id,
    'parent_transaction_id' => $debt->id,
    'payment_status' => Transaction::PAYMENT_STATUS_PAID,
    'user_id' => $admin->id,
    'metadata' => ['transaction_type' => 'debt_payment'],
]);

JournalEntry::query()->where('transaction_id', $payment->id)->update(['user_id' => $admin->id]);

echo "Debt owner: {$debt->fresh()->user_id}\n";
echo "Payment owner: {$payment->fresh()->user_id}\n";
echo "Journal entries:\n";
print_r(DB::table('journal_entries')->orderBy('transaction_id')->get()->toArray());
echo "Journal lines for payment:\n";
print_r(DB::table('journal_lines')
    ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
    ->where('journal_entries.transaction_id', $payment->id)
    ->select('journal_lines.account_id', 'journal_lines.debit', 'journal_lines.credit')
    ->get()->toArray());

echo "Trial balance query rows for accountant:\n";
print_r(DB::table('accounts')
    ->leftJoin('journal_lines', 'journal_lines.account_id', '=', 'accounts.id')
    ->leftJoin('journal_entries', function ($join) {
        $join->on('journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->whereBetween('journal_entries.entry_date', [now()->subDays(5)->toDateString(), now()->toDateString()]);
    })
    ->leftJoin('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
    ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
    ->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$accountant->id])
    ->where('accounts.is_active', true)
    ->select('accounts.code', 'accounts.name')
    ->selectRaw('COALESCE(SUM(CASE WHEN journal_entries.id IS NOT NULL THEN journal_lines.debit ELSE 0 END), 0) as debit_total')
    ->selectRaw('COALESCE(SUM(CASE WHEN journal_entries.id IS NOT NULL THEN journal_lines.credit ELSE 0 END), 0) as credit_total')
    ->groupBy('accounts.code', 'accounts.name')
    ->orderBy('accounts.code')
    ->get()->toArray());

echo "Joined payment transaction ownership rows:\n";
print_r(DB::table('journal_entries')
    ->join('transactions', 'transactions.id', '=', 'journal_entries.transaction_id')
    ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
    ->where('journal_entries.transaction_id', $payment->id)
    ->select(
        'journal_entries.transaction_id',
        'journal_entries.user_id as journal_user_id',
        'transactions.user_id as transaction_user_id',
        'transactions.parent_transaction_id',
        'parent_transactions.user_id as parent_user_id'
    )
    ->get()->toArray());