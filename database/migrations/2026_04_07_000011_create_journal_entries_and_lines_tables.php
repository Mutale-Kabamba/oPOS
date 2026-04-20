<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('reference', 80)->nullable();
            $table->string('description')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('memo')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'user_id']);
        });

        $assetClearingAccountId = $this->ensureAssetClearingAccount();
        $settlementAccountId = $this->resolveSettlementAccountId($assetClearingAccountId);

        $transactions = DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select(
                'transactions.id',
                'transactions.user_id',
                'transactions.account_id',
                'transactions.amount',
                'transactions.date',
                'transactions.description',
                'transactions.created_at',
                'transactions.updated_at',
                'accounts.type as account_type'
            )
            ->orderBy('transactions.id')
            ->get();

        foreach ($transactions as $transaction) {
            $amount = round((float) $transaction->amount, 2);

            if ($amount <= 0) {
                continue;
            }

            $transactionType = $this->inferTransactionType($transaction->account_type);
            $debitAccountId = $this->resolveDebitAccountId(
                $transactionType,
                (int) $transaction->account_id,
                $settlementAccountId,
                $assetClearingAccountId
            );
            $creditAccountId = $this->resolveCreditAccountId(
                $transactionType,
                (int) $transaction->account_id,
                $settlementAccountId,
                $assetClearingAccountId
            );

            $journalEntryId = DB::table('journal_entries')->insertGetId([
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'entry_date' => $transaction->date,
                'reference' => 'TXN-'.$transaction->id,
                'description' => $transaction->description,
                'posted_at' => $transaction->updated_at ?? $transaction->created_at,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ]);

            DB::table('journal_lines')->insert([
                [
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $debitAccountId,
                    'user_id' => $transaction->user_id,
                    'debit' => $amount,
                    'credit' => 0,
                    'memo' => 'Auto debit leg',
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ],
                [
                    'journal_entry_id' => $journalEntryId,
                    'account_id' => $creditAccountId,
                    'user_id' => $transaction->user_id,
                    'debit' => 0,
                    'credit' => $amount,
                    'memo' => 'Auto credit leg',
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
    }

    private function ensureAssetClearingAccount(): int
    {
        $existingId = DB::table('accounts')->where('code', '1199')->value('id');

        if ($existingId) {
            return (int) $existingId;
        }

        $now = now();

        return (int) DB::table('accounts')->insertGetId([
            'code' => '1199',
            'name' => 'Asset Clearing Account',
            'type' => 'asset',
            'group_name' => 'valuables',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function resolveSettlementAccountId(int $fallbackId): int
    {
        $accountId = DB::table('accounts')->where('code', '1100')->value('id');

        if ($accountId) {
            return (int) $accountId;
        }

        $assetAccountId = DB::table('accounts')
            ->where('type', 'asset')
            ->orderBy('code')
            ->value('id');

        return $assetAccountId ? (int) $assetAccountId : $fallbackId;
    }

    private function inferTransactionType(string $accountType): string
    {
        return match ($accountType) {
            'income' => 'money_in',
            'cogs' => 'money_out_direct',
            'expense' => 'money_out_general',
            'asset' => 'valuables',
            'liability' => 'debts',
            default => 'money_in',
        };
    }

    private function resolveDebitAccountId(string $transactionType, int $primaryAccountId, int $settlementAccountId, int $assetClearingAccountId): int
    {
        return match ($transactionType) {
            'money_in', 'debts' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            'money_out_direct', 'money_out_general', 'valuables' => $primaryAccountId,
            default => $primaryAccountId,
        };
    }

    private function resolveCreditAccountId(string $transactionType, int $primaryAccountId, int $settlementAccountId, int $assetClearingAccountId): int
    {
        return match ($transactionType) {
            'money_in', 'debts' => $primaryAccountId,
            'money_out_direct', 'money_out_general' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            'valuables' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            default => $settlementAccountId,
        };
    }
};