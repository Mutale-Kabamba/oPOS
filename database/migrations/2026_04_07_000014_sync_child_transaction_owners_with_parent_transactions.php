<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $childTransactions = DB::table('transactions as child')
            ->join('transactions as parent', 'parent.id', '=', 'child.parent_transaction_id')
            ->whereNotNull('child.parent_transaction_id')
            ->whereColumn('child.user_id', '!=', 'parent.user_id')
            ->select('child.id', 'parent.user_id as owner_user_id')
            ->get();

        foreach ($childTransactions as $childTransaction) {
            DB::table('transactions')
                ->where('id', $childTransaction->id)
                ->update(['user_id' => $childTransaction->owner_user_id]);

            DB::table('journal_entries')
                ->where('transaction_id', $childTransaction->id)
                ->update(['user_id' => $childTransaction->owner_user_id]);

            $journalEntryIds = DB::table('journal_entries')
                ->where('transaction_id', $childTransaction->id)
                ->pluck('id');

            if ($journalEntryIds->isNotEmpty()) {
                DB::table('journal_lines')
                    ->whereIn('journal_entry_id', $journalEntryIds)
                    ->update(['user_id' => $childTransaction->owner_user_id]);
            }
        }
    }

    public function down(): void
    {
        // Historical owner normalization is intentionally irreversible.
    }
};