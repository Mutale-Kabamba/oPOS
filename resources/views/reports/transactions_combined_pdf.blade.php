<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>oPOS | By Ori - Combined Transaction Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .logo-cell { width: 92px; text-align: right; vertical-align: top; }
        .logo { width: 76px; height: auto; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; letter-spacing: 0.4px; }
        .title { color: #0B4D2C; font-size: 20px; font-weight: 700; margin: 2px 0 2px; }
        .period { color: #4B5563; font-size: 11px; margin: 0; }
        .divider { border-top: 1px solid #0B4D2C; margin: 8px 0 10px; }
        .section-title { color: #0B4D2C; font-size: 13px; font-weight: 700; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; color: #0B4D2C; padding: 6px 5px; border-bottom: 1px solid #0B4D2C; }
        td { padding: 5px; border-bottom: 1px solid #E5E7EB; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #F4F7F5; }
        .amount { text-align: right; }
        .empty { color: #6B7280; padding: 8px 0 2px; }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 24px;
            right: 24px;
            color: #6B7280;
            font-size: 10px;
            border-top: 1px solid #D1D5DB;
            padding-top: 8px;
        }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { border: 0; padding: 0; vertical-align: middle; }
        .footer-right { text-align: right; }
        .footer-left { white-space: nowrap; vertical-align: middle; }
        .footer-brand { display: inline-flex; align-items: center; justify-content: center; gap: 0; line-height: 1.2; }
        .powered-by { display: inline; margin-top: 0; margin-left: 6px; }
        .powered-link { color: #6B7280; text-decoration: none; margin-left: 4px; }
        .powered-link:hover { color: #F97316; }
        .footer-logo { display: inline-block; height: 36px; width: auto; margin-bottom: 0; vertical-align: middle; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/kwatu_logo.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
        $footerLogoPath = public_path('images/color.png');
        if (! file_exists($footerLogoPath)) {
            $footerLogoPath = public_path('images/full_color.png');
        }
        $footerLogoData = file_exists($footerLogoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($footerLogoPath)) : null;
    @endphp

    <table class="header-table">
        <tr>
            <td>
                <div class="brand-name">OPOS | BY ORI</div>
                <div class="title">Combined Transaction Report</div>
                <p class="period">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </td>
            <td class="logo-cell">
                @if ($logoData)
                    <img src="{{ $logoData }}" alt="oPOS Logo" class="logo">
                @endif
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    @php
        $sections = [
            'Income Transactions' => $incomeRows,
            'Expense Transactions' => $expenseRows,
            'Full Transactions' => $fullRows,
        ];
    @endphp

    @foreach ($sections as $sectionTitle => $rows)
        <div class="section-title">{{ $sectionTitle }}</div>

        @if ($rows->isEmpty())
            <p class="empty">No records found for this section.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row->date->format('d M Y') }}</td>
                            <td>{{ $row->account?->code }} - {{ $row->account?->name }}</td>
                            <td>{{ strtoupper($row->account?->type ?? 'n/a') }}</td>
                            <td class="amount">{{ 'K ' . number_format($row->amount, 2) }}</td>
                            <td>{{ ucfirst($row->payment_status) }}</td>
                            <td>{{ $row->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="footer">
        <table>
            <tr>
                <td class="footer-left">
                    <div class="footer-brand">
                        @if ($footerLogoData)
                            <a href="https://www.oristudiozm.com"><img src="{{ $footerLogoData }}" alt="Ori Studio" class="footer-logo"></a>
                        @endif
                        <span class="powered-by">Powered By:</span>
                        <a href="https://www.oristudiozm.com" class="powered-link">Ori Studio Limited</a>
                    </div>
                </td>
                <td class="footer-right">oPOS V2.0</td>
            </tr>
        </table>
    </div>
</body>
</html>
