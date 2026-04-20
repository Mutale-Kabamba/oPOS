<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>oPOS | By Ori - Balance Sheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1A1A1A; margin: 24px; padding-bottom: 34px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .logo-cell { width: 92px; text-align: right; vertical-align: top; }
        .logo { width: 76px; height: auto; }
        .brand-name { color: #0B4D2C; font-size: 12px; font-weight: 700; letter-spacing: 0.4px; }
        .title { color: #0B4D2C; font-size: 22px; font-weight: 700; margin: 2px 0 2px; }
        .period { color: #4B5563; margin: 0 0 12px; font-size: 11px; }
        .section-title { color: #0B4D2C; font-size: 15px; font-weight: 700; border-bottom: 1px solid #0B4D2C; padding-bottom: 2px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        td { padding: 4px 0; font-size: 11px; }
        .label { padding-left: 14px; color: #374151; }
        .amount { text-align: right; width: 180px; }
        .total-row td { font-weight: 700; border-top: 1px solid #1A1A1A; padding-top: 5px; }
        .equation { margin-top: 10px; font-size: 11px; color: #374151; }
        .ok { color: #0B4D2C; font-weight: 700; }
        .warn { color: #FF6B35; font-weight: 700; }
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
                <div class="title">Balance Sheet</div>
                <p class="period">As of {{ \Carbon\Carbon::parse($asOf)->format('d M Y') }}</p>
            </td>
            <td class="logo-cell">
                @if ($logoData)
                    <img src="{{ $logoData }}" alt="oPOS Logo" class="logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Financial Position</div>
    <table>
        <tr>
            <td class="label">Total Valuables (Assets)</td>
            <td class="amount">{{ 'K ' . number_format($totalValuables, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Total Debts (Liabilities)</td>
            <td class="amount">({{ 'K ' . number_format($totalDebts, 2) }})</td>
        </tr>
        <tr class="total-row">
            <td class="label">Equity</td>
            <td class="amount">{{ 'K ' . number_format($equity, 2) }}</td>
        </tr>
    </table>

    <p class="equation">
        Equation check: Valuables = Debts + Equity
        ({{ 'K ' . number_format($totalValuables, 2) }} = {{ 'K ' . number_format($totalDebts + $equity, 2) }})
        @if (abs($equationGap) <= 0.0001)
            <span class="ok">Balanced</span>
        @else
            <span class="warn">Difference: {{ 'K ' . number_format($equationGap, 2) }}</span>
        @endif
    </p>

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
