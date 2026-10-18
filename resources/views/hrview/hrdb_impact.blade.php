<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Impact Score</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; padding:0 4% 20px; }
        .kpi-card { background:#fff; border-radius:16px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .kpi-label { color:#6a7e9a; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
        .kpi-value { font-size:28px; font-weight:700; color:#02338d; }
        .kpi-sub { font-size:12px; color:#6b7280; margin-top:4px; }
        .filter-row { padding:0 4% 20px; display:flex; gap:10px; flex-wrap:wrap; }
        .filter-btn { padding:8px 18px; border-radius:20px; border:2px solid #ccc; background:#fff; cursor:pointer; font-weight:600; font-size:13px; text-decoration:none; color:#333; }
        .filter-btn.active { background:#02338d; color:#fff; border-color:#02338d; }
        .table-wrap { padding:0 4% 40px; }
        .data-table { width:100%; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-collapse:collapse; }
        .data-table th { background:#02338d; color:#fff; padding:12px; text-align:left; font-size:13px; }
        .data-table td { padding:12px; border-bottom:1px solid #eee; font-size:14px; }
        .lift-positive { color:#0a7b2b; font-weight:700; }
        .lift-zero { color:#888; }
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
                <a href="{{ route('impact.analytics') }}" class="nav-link active"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon" id="impact-icon"> Impact</a>
                <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Wellness Impact Score</h1>
    <p style="padding-left:4%; color:#6b7280;">Based on the WHO-5 Well-Being Index. Pre vs post comparison per event.</p>

    <div class="filter-row">
        <a href="{{ route('impact.analytics') }}" class="filter-btn {{ !$activeCategory ? 'active' : '' }}">All</a>
        <a href="{{ route('impact.analytics', ['category' => 'physical']) }}"  class="filter-btn {{ $activeCategory === 'physical' ? 'active' : '' }}">Physical</a>
        <a href="{{ route('impact.analytics', ['category' => 'mental']) }}"    class="filter-btn {{ $activeCategory === 'mental' ? 'active' : '' }}">Mental</a>
        <a href="{{ route('impact.analytics', ['category' => 'financial']) }}" class="filter-btn {{ $activeCategory === 'financial' ? 'active' : '' }}">Financial</a>
        <a href="{{ route('impact.analytics', ['category' => 'social']) }}"    class="filter-btn {{ $activeCategory === 'social' ? 'active' : '' }}">Social</a>
    </div>

    <p class="section-title">AVERAGE LIFT BY CATEGORY</p>
    <div class="kpi-grid">
        @foreach($categories as $name => $data)
            <div class="kpi-card">
                <div class="kpi-label">{{ $name }}</div>
                <div class="kpi-value">{{ $data['avg_lift'] >= 0 ? '+' : '' }}{{ $data['avg_lift'] }}</div>
                <div class="kpi-sub">{{ $data['sample'] }} responses</div>
            </div>
        @endforeach
    </div>

    <p class="section-title">EVENT BREAKDOWN</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Event</th><th>Category</th><th>Pre Avg</th><th>Post Avg</th><th>Lift</th><th>Follow-up</th></tr>
            </thead>
            <tbody>
                @forelse($eventTable as $row)
                    <tr>
                        <td><strong>{{ $row['event'] }}</strong></td>
                        <td>{{ $row['category'] }}</td>
                        <td>{{ $row['pre_average'] ?? '—' }}</td>
                        <td>{{ $row['post_average'] ?? '—' }}</td>
                        <td class="{{ $row['lift'] > 0 ? 'lift-positive' : 'lift-zero' }}">{{ $row['lift'] > 0 ? '+' : '' }}{{ $row['lift'] }}</td>
                        <td>{{ $row['follow_up'] ? '✔' : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#888;">No events in this category yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>