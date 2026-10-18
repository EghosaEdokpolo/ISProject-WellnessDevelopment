<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departmental Engagement</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; padding:0 4% 20px; }
        .kpi-card { background:#fff; border-radius:16px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .kpi-label { color:#6a7e9a; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; }
        .kpi-value { font-size:28px; font-weight:700; color:#02338d; }
        .two-col { display:grid; grid-template-columns:1fr 1fr; gap:20px; padding:0 4% 20px; }
        @media (max-width:700px){ .two-col { grid-template-columns:1fr; } }
        .col-card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .col-card h3 { color:#02338d; margin:0 0 14px; font-size:15px; text-transform:uppercase; letter-spacing:0.5px; }
        .dept-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f0f0f0; }
        .dept-row:last-child { border-bottom:none; }
        .dept-name { color:#1f2937; font-weight:600; font-size:14px; }
        .dept-rate { font-weight:700; color:#02338d; }
        .pill-engaged { background:#d4f4dd; color:#0a7b2b; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; }
        .pill-developing { background:#fff4cf; color:#a87600; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; }
        .pill-disengaged { background:#ffe0e0; color:#b00; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; }
        .table-wrap { padding:0 4% 40px; }
        .data-table { width:100%; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-collapse:collapse; }
        .data-table th { background:#02338d; color:#fff; padding:12px; text-align:left; font-size:13px; }
        .data-table td { padding:12px; border-bottom:1px solid #eee; font-size:14px; }
        .export-btn { display:inline-block; margin:0 4% 20px; padding:10px 22px; background:#cca04a; color:#000; border-radius:10px; text-decoration:none; font-weight:700; font-size:14px; }
        .section-title { padding:0 4%; margin-top:20px; color:#02338d; font-size:18px; font-weight:700; }
    </style>
</head>
<body>
    <div class="header-title">
        <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" alt="Strathmore University Logo">
        Strathmore University - Wellness Programme Management & Impact System (WPMIS)
    </div>
    <header class="nav-head">
        <nav class="nav-menu">
            <div class="pillgroup1">
                <span class="category-label">Dashboards</span>
                <a href="{{ route('dashboard.hr') }}" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
                <a href="{{ route('impact.analytics') }}" class="nav-link"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon" id="impact-icon"> Impact</a>
                <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="{{ route('department.analysis') }}" class="nav-link active"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Departmental Engagement Growth</h1>
    <p style="padding:0 4%; color:#6b7280; font-size:14px;">Analysis of departments across the semester.</p>

    <p class="section-title">OVERVIEW</p>
    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Departments Tracked</div><div class="kpi-value">{{ count($ranking['all_departments']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Org-Wide Average Rate</div><div class="kpi-value">{{ count($ranking['all_departments']) ? round(collect($ranking['all_departments'])->avg('engagement_rate'), 1) : 0 }}%</div></div>
        <div class="kpi-card"><div class="kpi-label">Engaged Departments</div><div class="kpi-value">{{ collect($ranking['all_departments'])->where('status', 'engaged')->count() }}</div></div>
    </div>

    <div class="two-col">
        <div class="col-card">
            <h3>Most Active — Top 5</h3>
            @forelse($ranking['most_active'] as $d)
                <div class="dept-row"><span class="dept-name">{{ $d['department'] }}</span><span class="dept-rate">{{ $d['engagement_rate'] }}%</span></div>
            @empty
                <p style="color:#888;font-size:13px;">No data yet.</p>
            @endforelse
        </div>
        <div class="col-card">
            <h3>Needs Attention — Bottom 5</h3>
            @forelse($ranking['needs_attention'] as $d)
                <div class="dept-row"><span class="dept-name">{{ $d['department'] }}</span><span class="dept-rate">{{ $d['engagement_rate'] }}%</span></div>
            @empty
                <p style="color:#888;font-size:13px;">No data yet.</p>
            @endforelse
        </div>
    </div>

    <p class="section-title">RANKED TABLE</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Department</th><th>Code</th><th>Staff</th><th>Attendees</th><th>Rate</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($ranking['all_departments'] as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $d['department'] }}</strong></td>
                        <td>{{ $d['code'] }}</td>
                        <td>{{ $d['total_staff'] }}</td>
                        <td>{{ $d['unique_attendees'] }}</td>
                        <td><strong>{{ $d['engagement_rate'] }}%</strong></td>
                        <td>
                            @php $cls = $d['status'] === 'engaged' ? 'pill-engaged' : ($d['status'] === 'developing' ? 'pill-developing' : 'pill-disengaged'); @endphp
                            <span class="{{ $cls }}">{{ $d['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('reports.departmental.export') }}" class="export-btn">⬇ Export Report (CSV)</a>
</body>
</html>