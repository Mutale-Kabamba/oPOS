<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PostingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalPostingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_money_in_transactions_use_the_configured_settlement_account(): void
    {
        $user = $this->createUser();
        $settlementAccount = $this->createAccount('1110', 'Main Bank', 'asset', 'valuables');
        $contraAccount = $this->createAccount('1199', 'Asset Clearing', 'asset', 'valuables');
        $incomeAccount = $this->createAccount('4100', 'Sales Revenue', 'income', 'money_in');

        app(PostingRuleService::class)->update($settlementAccount->id, $contraAccount->id);

        Transaction::create([
            'amount' => 1250,
            'date' => now()->toDateString(),
            'account_id' => $incomeAccount->id,
            'payment_status' => 'paid',
            'user_id' => $user->id,
            'metadata' => ['transaction_type' => 'money_in'],
        ]);

        $entry = JournalEntry::with('lines')->firstOrFail();

        $this->assertCount(2, $entry->lines);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $settlementAccount->id,
            'debit' => 1250.00,
            'credit' => 0.00,
        ]);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $incomeAccount->id,
            'debit' => 0.00,
            'credit' => 1250.00,
        ]);
    }

    public function test_contra_account_is_used_when_the_primary_account_matches_the_settlement_account(): void
    {
        $user = $this->createUser();
        $settlementAccount = $this->createAccount('1110', 'Main Bank', 'asset', 'valuables');
        $contraAccount = $this->createAccount('1199', 'Asset Clearing', 'asset', 'valuables');

        app(PostingRuleService::class)->update($settlementAccount->id, $contraAccount->id);

        Transaction::create([
            'amount' => 400,
            'date' => now()->toDateString(),
            'account_id' => $settlementAccount->id,
            'payment_status' => 'paid',
            'user_id' => $user->id,
            'metadata' => ['transaction_type' => 'valuables'],
        ]);

        $entry = JournalEntry::with('lines')->firstOrFail();

        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $settlementAccount->id,
            'debit' => 400.00,
            'credit' => 0.00,
        ]);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $contraAccount->id,
            'debit' => 0.00,
            'credit' => 400.00,
        ]);
    }

    public function test_debt_payment_transactions_debit_the_liability_and_credit_settlement(): void
    {
        $user = $this->createUser();
        $settlementAccount = $this->createAccount('1110', 'Main Bank', 'asset', 'valuables');
        $contraAccount = $this->createAccount('1199', 'Asset Clearing', 'asset', 'valuables');
        $liabilityAccount = $this->createAccount('2100', 'Accounts Payable', 'liability', 'debts');

        app(PostingRuleService::class)->update($settlementAccount->id, $contraAccount->id);

        Transaction::create([
            'amount' => 200,
            'date' => now()->toDateString(),
            'account_id' => $liabilityAccount->id,
            'payment_status' => 'paid',
            'user_id' => $user->id,
            'metadata' => ['transaction_type' => 'debt_payment'],
        ]);

        $entry = JournalEntry::with('lines')->firstOrFail();

        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $liabilityAccount->id,
            'debit' => 200.00,
            'credit' => 0.00,
        ]);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $entry->id,
            'account_id' => $settlementAccount->id,
            'debit' => 0.00,
            'credit' => 200.00,
        ]);
    }

    private function createUser(): User
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