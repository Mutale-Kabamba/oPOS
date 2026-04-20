<?php

use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Statutory reminders command and schedule removed

Artisan::command('audit:normalize-transaction-descriptions', function () {
    $logs = AuditLog::query()
        ->whereIn('action', ['entry_create', 'entry_update', 'entry_delete'])
        ->orderBy('id')
        ->get();

    if ($logs->isEmpty()) {
        $this->info('No transaction audit logs found.');

        return;
    }

    $updated = 0;

    foreach ($logs as $log) {
        $description = (string) ($log->description ?? '');
        $normalized = null;

        if (str_starts_with($description, 'Created: ') || str_starts_with($description, 'Edited: ') || str_starts_with($description, 'Deleted: ')) {
            continue;
        }

        if ($log->action === 'entry_create') {
            if (preg_match('/recorded a \$([0-9,]+(?:\.[0-9]{1,2})?)\s+([A-Z_]+)\s+entry under\s+(.+)\./', $description, $matches)) {
                $amount = 'K'.rtrim(rtrim(number_format((float) str_replace(',', '', $matches[1]), 2, '.', ''), '0'), '.');
                $entryType = match (strtoupper($matches[2])) {
                    'INCOME' => 'Money In',
                    'COGS' => 'Money Out (Direct)',
                    'EXPENSE' => 'Money Out (General)',
                    'ASSET' => 'Valuables',
                    'LIABILITY' => 'Debts',
                    default => 'Entry',
                };
                $ledger = trim($matches[3]);

                $normalized = "Created: {$entryType} > {$ledger} > {$amount}";
            }
        }

        if ($log->action === 'entry_update') {
            if (preg_match('/updated transaction #(\d+) under\s+(.+)\./', $description, $matches)) {
                $transactionId = (int) $matches[1];
                $ledgerFromLog = trim($matches[2]);
                $tx = Transaction::with('account')->find($transactionId);

                if ($tx) {
                    $entryType = match ($tx->account?->type) {
                        'income' => 'Money In',
                        'cogs' => 'Money Out (Direct)',
                        'expense' => 'Money Out (General)',
                        'asset' => 'Valuables',
                        'liability' => 'Debts',
                        default => 'Entry',
                    };
                    $ledger = $tx->account?->name ?? $ledgerFromLog;
                    $amount = 'K'.rtrim(rtrim(number_format((float) $tx->amount, 2, '.', ''), '0'), '.');
                } else {
                    $entryType = 'Entry';
                    $ledger = $ledgerFromLog;
                    $amount = '-';
                }

                $normalized = "Edited: {$entryType} > {$ledger} > {$amount}";
            }
        }

        if ($log->action === 'entry_delete') {
            if (preg_match('/deleted transaction #(\d+) from\s+(.+)\./', $description, $matches)) {
                $transactionId = (int) $matches[1];
                $ledgerFromLog = trim($matches[2]);
                $tx = Transaction::with('account')->find($transactionId);

                if ($tx) {
                    $entryType = match ($tx->account?->type) {
                        'income' => 'Money In',
                        'cogs' => 'Money Out (Direct)',
                        'expense' => 'Money Out (General)',
                        'asset' => 'Valuables',
                        'liability' => 'Debts',
                        default => 'Entry',
                    };
                    $ledger = $tx->account?->name ?? $ledgerFromLog;
                    $amount = 'K'.rtrim(rtrim(number_format((float) $tx->amount, 2, '.', ''), '0'), '.');
                } else {
                    $entryType = 'Entry';
                    $ledger = $ledgerFromLog;
                    $amount = '-';
                }

                $normalized = "Deleted: {$entryType} > {$ledger} > {$amount}";
            }
        }

        if ($normalized && $normalized !== $description) {
            $log->update(['description' => $normalized]);
            $updated++;
        }
    }

    $this->info('Normalization complete. Updated logs: '.$updated);
})->purpose('Normalize historical transaction audit descriptions to compact format');

Artisan::command('audit:relabel-route-entry-actions', function () {
    $logs = AuditLog::query()
        ->whereIn('action', ['entry_post', 'entry_put', 'entry_patch', 'entry_delete'])
        ->where('description', 'like', 'Route:%')
        ->get();

    if ($logs->isEmpty()) {
        $this->info('No historical generic entry_* route logs found.');

        return;
    }

    $updated = 0;

    foreach ($logs as $log) {
        $newAction = str_replace('entry_', 'route_', (string) $log->action);

        if ($newAction === $log->action) {
            continue;
        }

        $log->update(['action' => $newAction]);
        $updated++;
    }

    $this->info('Relabel complete. Updated logs: '.$updated);
})->purpose('Relabel historical generic entry_* route logs to route_*');
