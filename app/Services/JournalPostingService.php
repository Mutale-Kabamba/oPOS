<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class JournalPostingService
{
    public function __construct(private readonly PostingRuleService $postingRuleService)
    {
    }

    public function sync(Transaction $transaction): void
    {
        $transaction->loadMissing('account');

        if (! $transaction->account) {
            return;
        }

        $amount = round((float) $transaction->amount, 2);

        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($transaction, $amount): void {
            $settlementAccount = $this->postingRuleService->settlementAccount();
            $assetClearingAccount = $this->postingRuleService->contraAccount();
            $transactionType = $this->resolveTransactionType($transaction);

            [$debitAccountId, $creditAccountId] = $this->resolveAccountPair(
                $transactionType,
                (int) $transaction->account_id,
                (int) $settlementAccount->id,
                (int) $assetClearingAccount->id,
            );

            $journalEntry = JournalEntry::query()->updateOrCreate(
                ['transaction_id' => $transaction->id],
                [
                    'user_id' => $transaction->user_id,
                    'entry_date' => $transaction->date,
                    'reference' => 'TXN-'.$transaction->id,
                    'description' => $transaction->description,
                    'posted_at' => now(),
                ]
            );

            $journalEntry->lines()->delete();

            $journalEntry->lines()->createMany([
                [
                    'account_id' => $debitAccountId,
                    'user_id' => $transaction->user_id,
                    'debit' => $amount,
                    'credit' => 0,
                    'memo' => 'Auto debit leg',
                ],
                [
                    'account_id' => $creditAccountId,
                    'user_id' => $transaction->user_id,
                    'debit' => 0,
                    'credit' => $amount,
                    'memo' => 'Auto credit leg',
                ],
            ]);
        });
    }

    private function resolveTransactionType(Transaction $transaction): string
    {
        $metadataType = $transaction->metadata['transaction_type'] ?? null;

        if (is_string($metadataType) && $metadataType !== '') {
            return $metadataType;
        }

        return match ($transaction->account->type) {
            'income' => 'money_in',
            'cogs' => 'money_out_direct',
            'expense' => 'money_out_general',
            'asset' => 'valuables',
            'liability' => 'debts',
            default => 'money_in',
        };
    }

    private function resolveAccountPair(string $transactionType, int $primaryAccountId, int $settlementAccountId, int $assetClearingAccountId): array
    {
        $debitAccountId = match ($transactionType) {
            'money_in', 'debts' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            'debt_payment' => $primaryAccountId,
            'money_out_direct', 'money_out_general', 'valuables' => $primaryAccountId,
            default => $primaryAccountId,
        };

        $creditAccountId = match ($transactionType) {
            'money_in', 'debts' => $primaryAccountId,
            'debt_payment' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            'money_out_direct', 'money_out_general' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            'valuables' => $primaryAccountId === $settlementAccountId ? $assetClearingAccountId : $settlementAccountId,
            default => $settlementAccountId,
        };

        return [$debitAccountId, $creditAccountId];
    }
}