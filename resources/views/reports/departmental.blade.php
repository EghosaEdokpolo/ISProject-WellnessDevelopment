<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Strathmore University — WPMIS Departmental Report</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 3px solid #02338d; padding-bottom: 14px; }
        .gold-bar { height: 4px; background-color: #c99a2e; width: 120px; margin: 0 auto 10px auto; }
        .header h1 { color: #02338d; margin: 0 0 4px 0; font-size: 20px; letter-spacing: 0.5px; }
        .header p { margin: 2px 0; color: #475569; font-size: 11px; }
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 20px -8px; }
        .kpi-box { border: 1px solid #cbd5e1; background-color: #f8fafc; padding: 10px; text-align: center; width: 25%; }
        .kpi-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .kpi-value { font-size: 16px; font-weight: bold; color: #02338d; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        table.data th, table.data td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        table.data th { background-color: #02338d; color: #ffffff; font-size: 10px; text-transform: uppercase; }
        table.data tr:nth-child(even) { background-color: #f8fafc; }
        .num { text-align: center !important; }
        .status-engaged { color: #047857; font-weight: bold; }
        .status-developing { color: #b45309; font-weight: bold; }
        .status-disengaged { color: #b91c1c; font-weight: bold; }
        .footer { margin-top: 24px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    @php $stats = $ranking['stats'] ?? []; @endphp

    <div class="header">
        <div class="gold-bar"></div>
        <h1>STRATHMORE UNIVERSITY</h1>
        <p><strong>People & Culture — Workplace Wellness Management Information System (WPMIS)</strong></p>
        <p>Departmental Engagement & Participation Report • Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <table class="kpi-table">
        <tr>
            <td class="kpi-box">
                <div class="kpi-label">Total Departments</div>
                <div class="kpi-value">{{ $stats['total_departments'] ?? 0 }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Org Average</div>
                <div class="kpi-value">{{ $stats['org_average'] ?? 0 }}%</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Engaged Depts</div>
                <div class="kpi-value" style="color: #047857;">{{ $stats['engaged_count'] ?? 0 }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Needs Attention</div>
                <div class="kpi-value" style="color: #b91c1c;">{{ $stats['disengaged_count'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Department</th>
                <th>Code</th>
                <th class="num">Total Staff</th>
                <th class="num">Unique Attendees</th>
                <th class="num">Engagement Rate</th>
                <th class="num">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking['all_departments'] ?? [] as $row)
                @php $statusKey = strtolower($row['status'] ?? 'developing'); @endphp
                <tr>
                    <td><strong>{{ $row['department'] }}</strong></td>
                    <td>{{ $row['code'] }}</td>
                    <td class="num">{{ $row['total_staff'] }}</td>
                    <td class="num">{{ $row['unique_attendees'] }}</td>
                    <td class="num"><strong>{{ $row['engagement_rate'] }}%</strong></td>
                    <td class="num status-{{ $statusKey }}">{{ strtoupper($statusKey) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="num">No departmental engagement records available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential — Strathmore University People & Culture Wellness Analytics • Kenya Data Protection Act Compliant
    </div>
</body>
</html>