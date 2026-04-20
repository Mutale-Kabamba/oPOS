<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Book System - Reconciliation Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .title { color: #0B4D2C; font-size: 22px; font-weight: 700; margin: 2px 0; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; }
        .period { color: #4B5563; margin: 0 0 10px; }
        .summary { margin: 8px 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #0B4D2C; padding: 6px 4px; border-bottom: 1px solid #0B4D2C; }
        td { padding: 5px 4px; border-bottom: 1px solid #E5E7EB; }
        .amount { text-align: right; }
        .footer { position: fixed; bottom: 10px; left: 24px; right: 24px; color: #6B7280; font-size: 10px; border-top: 1px solid #D1D5DB; padding-top: 8px; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
    <div class="brand-name">FINANCE BOOK SYSTEM</div>
    <div class="title">Reconciliation Report</div>
    <p class="period">
        Account: {{ $selectedAccount ? $selectedAccount->code . ' - ' . $selectedAccount->name : 'N/A' }}
        | As of {{ \Carbon\Carbon::parse($asOf)->format('d M Y') }}
    </p>

    <p class="summary">
        System Cleared Balance: {{ 'K ' . number_format($clearedBalance, 2) }}
        | Outstanding Uncleared: {{ 'K ' . number_format($unclearedBalance, 2) }}
        | Statement Ending Balance: {{ $statementEndingBalance === null ? '—' : 'K ' . number_format($statementEndingBalance, 2) }}
        | Variance: {{ $variance === null ? '—' : 'K ' . number_format($variance, 2) }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Supplier</th>
                <th>User</th>
                <th class="amount">Movement</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unclearedRows as $row)
                <tr>
                    <td>{{ $row->date->format('d M Y') }}</td>
                    <td>{{ $row->description ?: '—' }}</td>
                    <td>{{ $row->supplier?->name ?: '—' }}</td>
                    <td>{{ $row->user?->name ?: '—' }}</td>
                    <td class="amount">{{ 'K ' . number_format($row->movement_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>Powered By: Ori Studio Limited</td>
                <td class="footer-right">oPOS V2.0</td>
            </tr>
        </table>
    </div>
</body>
</html>