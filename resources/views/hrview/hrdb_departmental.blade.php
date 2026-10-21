<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departmental Engagement Growth</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        body { background: #f5f4f0; font-family: 'Lato', sans-serif; }
        .kpi-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 4% 20px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; }
        .metric-value { font-size: 32px; font-weight: 800; color: #02338d; margin: 10px 0 4px; }
        .metric-sub { font-size: 12px; color: #9ca3af; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 0 4% 40px; }
        .list-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .list-item:last-child { border-bottom: none; }
        .data-table { width:100%; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-collapse:collapse; }
        .data-table th { background:#02338d; color:#fff; padding:12px; text-align:left; font-size:12px; font-weight:600; }
        .data-table td { padding:12px; border-bottom:1px solid #eee; font-size:13px; color:#374151; }
        .export-btn { display:inline-block; padding:10px 22px; background:#cca04a; color:#000; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px; border: none; cursor: pointer;}
    </style>
</head>
<body>
    <div class="header-title"><img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}"> WPMIS — WELLNESS PROGRAMME MANAGEMENT & IMPACT SYSTEM</div>
    <header class="nav-head"><nav class="nav-menu">
        <div class="pillgroup1"><span class="category-label">Dashboards</span>
            <a href="{{ route('dashboard.hr') }}" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
            <a href="{{ route('impact.analytics') }}" class="nav-link"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon"> Impact</a>
            <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon"> Retention</a>
            <a href="{{ route('department.analysis') }}" class="nav-link active"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
        </div>
        <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
        <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
        <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
    </nav></header>

    <div style="padding: 0 4%; margin-bottom: 20px;">
        <h1 style="margin-bottom: 5px;">Departmental Engagement Growth <span style="font-size:12px; background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:12px; vertical-align:middle;">● Lagging</span></h1>
        <p style="margin-top:0; color:#6b7280; font-size:14px;">Which departments moved from disengaged to engaged across the semester.</p>
    </div>

    <div class="kpi-grid-4">
        <div class="card"><div class="card-label">DEPARTMENTS TRACKED</div><div class="metric-value" style="color:#02338d;">{{ count($ranking['all_departments']) }}</div><div class="metric-sub">of 47 total</div></div>
        <div class="card"><div class="card-label">ORG AVG — SEMESTER START</div><div class="metric-value" style="color:#b91c1c;">30%</div><div class="metric-sub">engagement rate</div></div>
        <div class="card"><div class="card-label">ORG AVG — SEMESTER END</div><div class="metric-value" style="color:#0a7b2b;">60%</div><div class="metric-sub">engagement rate</div></div>
        <div class="card"><div class="card-label">ORG-WIDE GROWTH</div><div class="metric-value" style="color:#cca04a;">+30pp</div><div class="metric-sub">percentage points</div></div>
    </div>

    <div class="two-col">
        <div class="card">
            <div class="card-label" style="margin-bottom: 15px;">⭐ Most Active — Top 5</div>
            @forelse($ranking['most_active'] as $i => $d)
                <div class="list-item"><span><strong style="color:#9ca3af; margin-right:10px;">{{ $i+1 }}</strong> {{ $d['department'] }}</span><span style="font-weight:700;">{{ $d['engagement_rate'] }}%</span></div>
            @empty
                <p style="font-size:13px; color:#6b7280;">No data yet.</p>
            @endforelse
        </div>
        <div class="card">
            <div class="card-label" style="margin-bottom: 15px;">⚠️ Needs Attention — Bottom 5</div>
            @forelse($ranking['needs_attention'] as $i => $d)
                <div class="list-item"><span><strong style="color:#9ca3af; margin-right:10px;">{{ $i+1 }}</strong> {{ $d['department'] }}</span><span style="font-weight:700;">{{ $d['engagement_rate'] }}%</span></div>
            @empty
                <p style="font-size:13px; color:#6b7280;">No data yet.</p>
            @endforelse
        </div>
    </div>

    <div style="padding: 0 4% 20px; display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; gap:10px;">
            <button style="padding:8px 16px; border-radius:6px; border:none; background:#02338d; color:#fff; font-weight:600; font-size:12px;">Engagement Heatmap</button>
            <button style="padding:8px 16px; border-radius:6px; border:1px solid #d1d5db; background:#fff; color:#4b5563; font-weight:600; font-size:12px;">Ranked Table</button>
        </div>

        <!-- EXPORT BUTTONS -->
        <div style="display:flex; gap:10px;">
            <a href="{{ route('reports.departmental.export') }}" class="export-btn">⬇ Export CSV</a>
            <a href="{{ route('reports.pdf.export') }}" class="export-btn" style="background:#b91c1c; color:#fff;">📄 Download PDF</a>
        </div>
    </div>

    <div style="padding: 0 4% 40px;">
        <table class="data-table">
            <thead><tr><th>#</th><th>Department</th><th>Code</th><th>Staff</th><th>Attendees</th><th>Rate</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($ranking['all_departments'] as $i => $d)
                    <tr>
                        <td>{{ $i+1 }}</td><td><strong>{{ $d['department'] }}</strong></td><td>{{ $d['code'] }}</td>
                        <td>{{ $d['total_staff'] }}</td><td>{{ $d['unique_attendees'] }}</td><td><strong>{{ $d['engagement_rate'] }}%</strong></td>
                        <td><span style="background: {{ $d['status'] === 'engaged' ? '#dcfce7' : ($d['status'] === 'developing' ? '#fef3c7' : '#fee2e2') }}; color: {{ $d['status'] === 'engaged' ? '#166534' : ($d['status'] === 'developing' ? '#92400e' : '#991b1b') }}; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700; text-transform:uppercase;">{{ $d['status'] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>