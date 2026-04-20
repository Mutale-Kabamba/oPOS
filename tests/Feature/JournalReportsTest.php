<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\JournalEntry;
use App\Services\PostingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_trial_balance_report_uses_balanced_journal_totals(): void
    {
        $admin = $this->createAccountant();
        $settlement = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contra = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $income = $this->createAccount('4100', 'Revenue', 'income', 'money_in');

        app(PostingRuleService::class)->update($settlement->id, $contra->id);

        Transaction::create([
            'amount' => 500,
            'date' => now()->toDateString(),
            'account_id' => $income->id,
            'payment_status' => 'paid',
            'user_id' => $admin->id,
            'metadata' => ['transaction_type' => 'money_in'],
        ]);

        $response = $this->actingAs($admin)->get(route('reports.trial-balance'));

        $response->assertOk();
        $response->assertSee('500.00');
        $response->assertSee('0.00');
    }

    public function test_sales_report_is_generated_from_journal_income_lines(): void
    {
        $admin = $this->createAdmin();
        $settlement = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contra = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $income = $this->createAccount('4100', 'Revenue', 'income', 'money_in');
        $category = Category::create([
            'name' => 'Retail',
            'type' => 'income',
            'description' => 'Retail sales',
        ]);

        app(PostingRuleService::class)->update($settlement->id, $contra->id);

        Transaction::create([
            'amount' => 725,
            'date' => now()->toDateString(),
            'account_id' => $income->id,
            'category_id' => $category->id,
            'payment_status' => 'paid',
            'user_id' => $admin->id,
            'metadata' => ['transaction_type' => 'money_in'],
        ]);

        $response = $this->actingAs($admin)->get(route('reports.sales'));

        $response->assertOk();
        $response->assertSee('725.00');
        $response->assertSee('Retail');
    }

    public function test_reconciliation_uses_asset_account_journal_movements_and_can_clear_items(): void
    {
        $admin = $this->createAccountant();
        $settlement = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contra = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $income = $this->createAccount('4100', 'Revenue', 'income', 'money_in');

        app(PostingRuleService::class)->update($settlement->id, $contra->id);

        $transaction = Transaction::create([
            'amount' => 300,
            'date' => now()->toDateString(),
            'account_id' => $income->id,
            'payment_status' => 'paid',
            'user_id' => $admin->id,
            'metadata' => ['transaction_type' => 'money_in'],
        ]);

        $response = $this->actingAs($admin)->get(route('reports.reconciliation', [
            'account_id' => $settlement->id,
            'as_of' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('300.00');

        $updateResponse = $this->actingAs($admin)->post(route('reports.reconciliation.reconcile'), [
            'account_id' => $settlement->id,
            'as_of' => now()->toDateString(),
            'statement_ending_balance' => 300,
            'transaction_ids' => [$transaction->id],
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'is_reconciled' => true,
        ]);
    }

    public function test_trial_balance_includes_supplier_debt_and_payment_based_on_effective_owner_not_recorder(): void
    {
        $accountant = User::factory()->create([
            'role' => 'accountant',
            'is_active' => true,
        ]);
        $admin = $this->createAdmin();
        $settlement = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contra = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $liability = $this->createAccount('2100', 'Supplier Bills', 'liability', 'debts');

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

        $payment->refresh();
        $this->assertSame($accountant->id, $payment->user_id);
        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $payment->id,
            'user_id' => $accountant->id,
        ]);

        $response = $this->actingAs($accountant)->get(route('reports.trial-balance', [
            'from' => now()->subDays(5)->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('Supplier Bills');
        $response->assertSee('2,500.00');
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createAccountant(): User
    {
        return User::factory()->create([
            'role' => 'accountant',
            'is_active' => true,
        ]);
    }

    private function createAccount(string $code, string $name, string $type, string $groupName): Account
    {
        return Account::query()->updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'type' => $type,
                'group_name' => $groupName,
                'is_active' => true,
            ]
        );
    }
}