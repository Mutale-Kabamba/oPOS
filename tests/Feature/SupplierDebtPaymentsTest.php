<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\AccountingDashboardController;
use App\Services\PostingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierDebtPaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_debt_entries_created_from_accounting_form_are_forced_to_pending(): void
    {
        $user = $this->createAccountant();
        $liabilityAccount = $this->createAccount('2100', 'Accounts Payable', 'liability', 'debts');
        $supplier = Supplier::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post(route('accounting.transactions.store'), [
            'transaction_type' => 'debts',
            'amount' => 600,
            'date' => now()->toDateString(),
            'account_id' => $liabilityAccount->id,
            'supplier_id' => $supplier->id,
            'payment_status' => 'paid',
            'description' => 'Supplier invoice',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'account_id' => $liabilityAccount->id,
            'supplier_id' => $supplier->id,
            'payment_status' => Transaction::PAYMENT_STATUS_PENDING,
            'parent_transaction_id' => null,
        ]);
    }

    public function test_supplier_payment_route_creates_child_payment_and_updates_parent_statuses(): void
    {
        $user = $this->createAccountant();
        $settlementAccount = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contraAccount = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $liabilityAccount = $this->createAccount('2100', 'Accounts Payable', 'liability', 'debts');
        $supplier = Supplier::factory()->create(['is_active' => true]);

        app(PostingRuleService::class)->update($settlementAccount->id, $contraAccount->id);

        $debt = Transaction::create([
            'amount' => 500,
            'date' => now()->subDays(10)->toDateString(),
            'account_id' => $liabilityAccount->id,
            'supplier_id' => $supplier->id,
            'payment_status' => Transaction::PAYMENT_STATUS_PENDING,
            'user_id' => $user->id,
            'metadata' => ['transaction_type' => 'debts'],
        ]);

        $partialResponse = $this->actingAs($user)->post(route('reports.suppliers-aging.payments', $debt), [
            'amount' => 200,
            'date' => now()->toDateString(),
            'description' => 'First payment',
            'as_of' => now()->toDateString(),
        ]);

        $partialResponse->assertRedirect();

        $debt->refresh();
        $this->assertSame(Transaction::PAYMENT_STATUS_PARTIALLY_PAID, $debt->payment_status);
        $this->assertSame(300.0, $debt->remainingAmount());
        $this->assertDatabaseHas('transactions', [
            'parent_transaction_id' => $debt->id,
            'payment_status' => Transaction::PAYMENT_STATUS_PAID,
            'amount' => 200.00,
        ]);

        $finalResponse = $this->actingAs($user)->post(route('reports.suppliers-aging.payments', $debt), [
            'amount' => 300,
            'date' => now()->toDateString(),
            'description' => 'Final payment',
            'as_of' => now()->toDateString(),
        ]);

        $finalResponse->assertRedirect();

        $debt->refresh();
        $this->assertSame(Transaction::PAYMENT_STATUS_PAID, $debt->payment_status);
        $this->assertSame(0.0, $debt->remainingAmount());
    }

    public function test_admin_recorded_payment_is_owned_by_original_accountant_and_recent_amount_uses_remaining_balance(): void
    {
        $accountant = $this->createAccountant();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $settlementAccount = $this->createAccount('1100', 'Cash', 'asset', 'valuables');
        $contraAccount = $this->createAccount('1199', 'Clearing', 'asset', 'valuables');
        $liabilityAccount = $this->createAccount('2100', 'Accounts Payable', 'liability', 'debts');
        $supplier = Supplier::factory()->create(['is_active' => true]);

        app(PostingRuleService::class)->update($settlementAccount->id, $contraAccount->id);

        $debt = Transaction::create([
            'amount' => 5000,
            'date' => now()->subDays(3)->toDateString(),
            'account_id' => $liabilityAccount->id,
            'supplier_id' => $supplier->id,
            'payment_status' => Transaction::PAYMENT_STATUS_PENDING,
            'user_id' => $accountant->id,
            'metadata' => ['transaction_type' => 'debts'],
        ]);

        $this->actingAs($admin)->post(route('reports.suppliers-aging.payments', $debt), [
            'amount' => 2500,
            'date' => now()->toDateString(),
            'description' => 'Admin recorded payment',
            'as_of' => now()->toDateString(),
        ])->assertRedirect();

        $payment = Transaction::query()->where('parent_transaction_id', $debt->id)->firstOrFail();
        $this->assertSame($accountant->id, $payment->user_id);

        $debt->load('account');
        $debt->loadSum('paymentTransactions as paid_amount', 'amount');
        $this->assertSame(2500.0, $debt->displayAmount());
        $this->assertSame(Transaction::PAYMENT_STATUS_PARTIALLY_PAID, $debt->fresh()->payment_status);
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