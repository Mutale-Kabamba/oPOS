<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Book System - Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .title { color: #0B4D2C; font-size: 22px; font-weight: 700; margin: 2px 0; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; }
        .period { color: #4B5563; margin: 0 0 10px; }
        .section-title { margin: 12px 0 6px; color: #0B4D2C; font-weight: 700; border-bottom: 1px solid #0B4D2C; padding-bottom: 2px; }
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
    <div class="title">Sales Report</div>
    <p class="period">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    <p>Total Revenue: {{ 'K ' . number_format($totalSales, 2) }}</p>

    <div class="section-title">Daily Trend</div>
    <table>
        <thead><tr><th>Period</th><th class="amount">Transactions</th><th class="amount">Revenue</th></tr></thead>
        <tbody>
            @foreach ($dailyRows as $row)
                <tr><td>{{ $row->period }}</td><td class="amount">{{ $row->transactions_count }}</td><td class="amount">{{ 'K ' . number_format($row->total_amount, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Monthly Trend</div>
    <table>
        <thead><tr><th>Period</th><th class="amount">Transactions</th><th class="amount">Revenue</th></tr></thead>
        <tbody>
            @foreach ($monthlyRows as $row)
                <tr><td>{{ $row->period }}</td><td class="amount">{{ $row->transactions_count }}</td><td class="amount">{{ 'K ' . number_format($row->total_amount, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">By Category</div>
    <table>
        <thead><tr><th>Category</th><th class="amount">Transactions</th><th class="amount">Revenue</th></tr></thead>
        <tbody>
            @foreach ($categoryRows as $row)
                <tr><td>{{ $row->category_name }}</td><td class="amount">{{ $row->transactions_count }}</td><td class="amount">{{ 'K ' . number_format($row->total_amount, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">By User</div>
    <table>
        <thead><tr><th>User</th><th class="amount">Transactions</th><th class="amount">Revenue</th></tr></thead>
        <tbody>
            @foreach ($userRows as $row)
                <tr><td>{{ $row->name }}</td><td class="amount">{{ $row->transactions_count }}</td><td class="amount">{{ 'K ' . number_format($row->total_amount, 2) }}</td></tr>
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