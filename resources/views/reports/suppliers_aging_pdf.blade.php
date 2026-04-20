<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Finance Book System - Suppliers Balance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .title { color: #0B4D2C; font-size: 22px; font-weight: 700; margin: 2px 0; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; }
        .period { color: #4B5563; margin: 0 0 10px; }
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
    <div class="title">Suppliers Balance Report</div>
    <p class="period">As of {{ \Carbon\Carbon::parse($asOf)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Contact</th>
                <th class="amount">Current</th>
                <th class="amount">1-30</th>
                <th class="amount">31-60</th>
                <th class="amount">61-90</th>
                <th class="amount">90+</th>
                <th class="amount">Total Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->supplier_name }}</td>
                    <td>{{ $row->contact_person ?: '—' }}</td>
                    <td class="amount">{{ $row->current > 0 ? 'K ' . number_format($row->current, 2) : '—' }}</td>
                    <td class="amount">{{ $row->days_1_30 > 0 ? 'K ' . number_format($row->days_1_30, 2) : '—' }}</td>
                    <td class="amount">{{ $row->days_31_60 > 0 ? 'K ' . number_format($row->days_31_60, 2) : '—' }}</td>
                    <td class="amount">{{ $row->days_61_90 > 0 ? 'K ' . number_format($row->days_61_90, 2) : '—' }}</td>
                    <td class="amount">{{ $row->days_90_plus > 0 ? 'K ' . number_format($row->days_90_plus, 2) : '—' }}</td>
                    <td class="amount">{{ 'K ' . number_format($row->total_due, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($debts->isNotEmpty())
        <div style="margin-top: 18px;" class="title">Outstanding Debt Detail</div>
        <table>
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th class="amount">Original</th>
                    <th class="amount">Paid</th>
                    <th class="amount">Remaining</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($debts as $debt)
                    <tr>
                        <td>{{ $debt->supplier?->name ?? 'Unknown Supplier' }}</td>
                        <td>{{ optional($debt->date)->format('d M Y') }}</td>
                        <td>{{ $debt->account_name ?? '—' }}</td>
                        <td>{{ $debt->description ?: '—' }}</td>
                        <td class="amount">{{ 'K ' . number_format($debt->original_amount, 2) }}</td>
                        <td class="amount">{{ $debt->paid_amount > 0 ? 'K ' . number_format($debt->paid_amount, 2) : '—' }}</td>
                        <td class="amount">{{ 'K ' . number_format($debt->remaining_amount, 2) }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($debt->payment_status)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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