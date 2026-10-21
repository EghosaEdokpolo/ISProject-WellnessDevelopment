<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness Impact Score</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <!-- Strathmore Brand Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        body { background: #f5f4f0; font-family: 'Lato', sans-serif; }
        .filter-row { padding: 0 4% 20px; display: flex; gap: 8px; }
        .pill { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-decoration: none; color: #4b5563; background: #fff; border: 1px solid #d1d5db; }
        .pill.active { background: #02338d; color: #fff; border-color: #02338d; }
        .kpi-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 4% 20px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; }
        .metric-value { font-size: 32px; font-weight: 800; color: #02338d; margin: 10px 0 4px; }
        .metric-sub { font-size: 12px; color: #9ca3af; }
        .data-table { width:100%; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-collapse:collapse; }
        .data-table th { background:#02338d; color:#fff; padding:12px; text-align:left; font-size:12px; font-weight:600; }
        .data-table td { padding:12px; border-bottom:1px solid #eee; font-size:13px; color:#374151; }
        .lift-positive { color:#0a7b2b; font-weight:700; }
    </style>
</head>
<body>
    <div class="header-title"><img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}"> WPMIS — WELLNESS PROGRAMME MANAGEMENT & IMPACT SYSTEM</div>
    <header class="nav-head"><nav class="nav-menu">
        <div class="pillgroup1"><span class="category-label">Dashboards</span>
            <a href="{{ route('dashboard.hr') }}" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
            <a href="{{ route('impact.analytics') }}" class="nav-link active"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon"> Impact</a>
            <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon"> Retention</a>
            <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
        </div>
        <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
        <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
        <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
    </nav></header>

    <div style="padding: 0 4%; margin-bottom: 20px;">
        <h1 style="margin-bottom: 5px;">Wellness Impact Score <span style="font-size:12px; background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:12px; vertical-align:middle;">● Lagging</span></h1>
        <p style="margin-top:0; color:#6b7280; font-size:14px;">WHO-5 Well-Being Index — validated instrument (score 0–25). Score below 13 indicates poor wellbeing. Pre vs post comparison per event.</p>
    </div>

    <div class="filter-row">
        <a href="{{ route('impact.analytics') }}" class="pill {{ !$activeCategory ? 'active' : '' }}">All</a>
        <a href="{{ route('impact.analytics', ['category' => 'physical']) }}" class="pill {{ $activeCategory === 'physical' ? 'active' : '' }}">Physical</a>
        <a href="{{ route('impact.analytics', ['category' => 'mental']) }}" class="pill {{ $activeCategory === 'mental' ? 'active' : '' }}">Mental</a>
        <a href="{{ route('impact.analytics', ['category' => 'financial']) }}" class="pill {{ $activeCategory === 'financial' ? 'active' : '' }}">Financial</a>
        <a href="{{ route('impact.analytics', ['category' => 'social']) }}" class="pill {{ $activeCategory === 'social' ? 'active' : '' }}">Social</a>
    </div>

    @if($highestDim && $lowestDim)
    <div style="padding: 0 4% 20px;">
        <div class="card" style="background: #f0fdf4; border-color: #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="margin: 0; font-weight: 700; color: #166534; font-size: 14px;">Highest Impact Dimension</p>
                <p style="margin: 0; font-size: 20px; font-weight: 800; color: #166534; text-transform: capitalize;">{{ $highestDim }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; font-weight: 700; color: #991b1b; font-size: 14px;">Lowest Impact Dimension</p>
                <p style="margin: 0; font-size: 20px; font-weight: 800; color: #991b1b; text-transform: capitalize;">{{ $lowestDim }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="kpi-grid-4">
        <div class="card"><div class="card-label">AVG WHO-5 LIFT</div><div class="metric-value" style="color:#0a7b2b;">+{{ $categories['mental']['avg_lift'] ?? 0 }}</div><div class="metric-sub">points gained (0–25 scale)</div></div>
        <div class="card"><div class="card-label">AVG SATISFACTION</div><div class="metric-value">4.3/5</div><div class="metric-sub">across filtered events</div></div>
        <div class="card"><div class="card-label">4–6 WK FOLLOW-UP</div><div class="metric-value" style="color:#cca04a;">17.2</div><div class="metric-sub">sustained wellbeing</div></div>
        <div class="card"><div class="card-label">EVENTS IN VIEW</div><div class="metric-value">{{ count($eventTable) }}</div><div class="metric-sub">of {{ count($eventTable) }} this semester</div></div>
    </div>

    <div class="card" style="margin: 0 4% 20px;">
        <div class="card-label">WHO-5 Pre / Post / Follow-up by Event</div>
        <div class="chart-placeholder" style="height:150px; display:flex; align-items:flex-end; justify-content:space-around; background:#f9fafb; margin-top:15px; border-radius:8px;">
             <div style="width:20px; height:40%; background:#e5e7eb;"></div><div style="width:20px; height:60%; background:#02338d;"></div>
             <div style="width:20px; height:50%; background:#e5e7eb;"></div><div style="width:20px; height:70%; background:#02338d;"></div>
             <div style="width:20px; height:30%; background:#e5e7eb;"></div><div style="width:20px; height:80%; background:#02338d;"></div>
        </div>
    </div>

    <div style="padding: 0 4% 40px;">
        <table class="data-table">
            <thead><tr><th>Event</th><th>Category</th><th>Pre Avg</th><th>Post Avg</th><th>Lift</th><th>Follow-up</th></tr></thead>
            <tbody>
                @forelse($eventTable as $row)
                    <tr>
                        <td><strong>{{ $row['event'] }}</strong></td><td>{{ ucfirst($row['category']) }}</td>
                        <td>{{ $row['pre_average'] ?? '—' }}</td><td>{{ $row['post_average'] ?? '—' }}</td>
                        <td class="{{ $row['lift'] > 0 ? 'lift-positive' : '' }}">{{ $row['lift'] > 0 ? '+' : '' }}{{ $row['lift'] }}</td>
                        <td>{{ $row['follow_up'] ? '✔' : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>