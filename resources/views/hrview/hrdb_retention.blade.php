<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustained Participation Rate</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .big-kpi { margin:20px 4%; background:linear-gradient(135deg,#02338d,#0a56c4); color:#fff; padding:40px; border-radius:20px; text-align:center; }
        .big-kpi .number { font-size:72px; font-weight:800; line-height:1; }
        .big-kpi .label { font-size:16px; opacity:0.9; margin-top:10px; }
        .big-kpi .sub { font-size:13px; opacity:0.75; margin-top:6px; }
        .sub-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; padding:0 4% 40px; }
        .sub-kpi { background:#fff; padding:20px; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .sub-kpi .num { font-size:32px; font-weight:700; color:#02338d; }
        .sub-kpi .lab { color:#6a7e9a; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; }
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
                <a href="{{ route('retention.metrics') }}" class="nav-link active"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Sustained Participation Rate</h1>
    <p style="padding:0 4%; color:#6b7280; font-size:14px;">% of staff attending more than one wellness event per academic year.</p>

    <div class="big-kpi">
        <div class="number">{{ $retention['sustained_participation'] }}%</div>
        <div class="label">Sustained Participation Rate</div>
        <div class="sub">{{ $retention['period']['start'] }} → {{ $retention['period']['end'] }}</div>
    </div>

    <div class="sub-kpis">
        <div class="sub-kpi">
            <div class="lab">Active Staff</div>
            <div class="num">{{ $retention['total_active_staff'] }}</div>
        </div>
        <div class="sub-kpi">
            <div class="lab">Sustained Attendees</div>
            <div class="num">{{ $retention['sustained_attendees'] }}</div>
        </div>
    </div>
</body>
</html>