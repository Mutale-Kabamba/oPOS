<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Book System - Trial Balance</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .title { color: #0B4D2C; font-size: 22px; font-weight: 700; margin: 2px 0; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; letter-spacing: 0.4px; }
        .period { color: #4B5563; margin: 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #0B4D2C; padding: 6px 5px; border-bottom: 1px solid #0B4D2C; }
        td { padding: 5px; border-bottom: 1px solid #E5E7EB; }
        .amount { text-align: right; }
        .totals td { font-weight: 700; border-top: 1px solid #0B4D2C; }
        .summary { margin: 10px 0 12px; color: #374151; }
        .footer { position: fixed; bottom: 10px; left: 24px; right: 24px; color: #6B7280; font-size: 10px; border-top: 1px solid #D1D5DB; padding-top: 8px; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; vertical-align: middle; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="brand-name">FINANCE BOOK SYSTEM</div>
                <div class="title">Trial Balance</div>
                <p class="period">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <p class="summary">
        Total Debits: {{ 'K ' . number_format($totalDebits, 2) }}
        | Total Credits: {{ 'K ' . number_format($totalCredits, 2) }}
        | Difference: {{ 'K ' . number_format($difference, 2) }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Account</th>
                <th>Type</th>
                <th class="amount">Debit</th>
                <th class="amount">Credit</th>
                <th class="amount">Closing</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ strtoupper($row->type) }}</td>
                    <td class="amount">{{ $row->debit > 0 ? 'K ' . number_format($row->debit, 2) : '—' }}</td>
                    <td class="amount">{{ $row->credit > 0 ? 'K ' . number_format($row->credit, 2) : '—' }}</td>
                    <td class="amount">{{ 'K ' . number_format($row->closing_balance, 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals">
                <td colspan="3">Totals</td>
                <td class="amount">{{ 'K ' . number_format($totalDebits, 2) }}</td>
                <td class="amount">{{ 'K ' . number_format($totalCredits, 2) }}</td>
                <td></td>
            </tr>
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