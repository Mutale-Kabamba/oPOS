<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Activity Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1A1A1A; font-size: 11px; margin: 24px; padding-bottom: 34px; }
        h1 { margin: 0 0 6px; color: #0B4D2C; font-size: 16px; }
        .meta { margin-bottom: 10px; color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d9e2dc; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #F4F7F5; color: #334155; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr:nth-child(even) td { background: #fafcfb; }
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
        $footerLogoPath = public_path('images/color.png');
        if (! file_exists($footerLogoPath)) {
            $footerLogoPath = public_path('images/full_color.png');
        }
        $footerLogoData = file_exists($footerLogoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($footerLogoPath)) : null;
    @endphp

    <h1>User Activity Report</h1>
    <div class="meta">Generated at {{ now()->format('d M Y H:i') }} | Total rows: {{ $activity->count() }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">When</th>
                <th style="width: 24%;">User</th>
                <th style="width: 24%;">Action</th>
                <th style="width: 30%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activity as $log)
                <tr>
                    <td>{{ $log->occurred_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($log->action)) }}</td>
                    <td>{{ $log->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">No activity available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
