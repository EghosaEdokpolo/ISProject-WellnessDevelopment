<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 24px; padding: 20px 4% 40px; }
        .card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card h2 { color: #02338d; margin: 0 0 8px; font-size: 18px; }
        .card p.desc { color: #6b7280; font-size: 13px; margin-bottom: 20px; }
        .info-box { background: #f5f4f0; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        .info-box .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e5e5; font-size: 14px; }
        .info-box .row:last-child { border-bottom: none; }
        .info-box .key { color: #6b7280; font-weight: 500; }
        .info-box .val { color: #1f2937; font-weight: 600; text-align: right; }
        .btn { display: block; width: 100%; padding: 14px; font-size: 15px; font-weight: 700; border-radius: 12px; cursor: pointer; border: none; margin-top: 8px; }
        .btn-reset { background: #fee; color: #b00; border: 2px solid #b00; }
        .btn-reset:active { background: #fdd; }
        .btn-logout { background: #f91c1c; color: #fff; }
        .btn-logout:active { background: #a21212; }
        .dp-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .dp-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .dp-table td:first-child { color: #6b7280; width: 40%; }
        .dp-table td:last-child { color: #1f2937; font-weight: 500; }
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
                <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link active"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Settings</h1>

    <div class="grid">
        <div class="card">
            <h2>Points Reset Control</h2>
            <p class="desc">Points reset annually. This action is irreversible; all staff balances will be zeroed.</p>

            <div class="info-box">
                @php
                    $month = (int) date('n');
                    $year  = (int) date('Y');
                    $cycleStart = $month >= 9 ? $year : $year - 1;
                    $cycleEnd   = $cycleStart + 1;
                @endphp
                <div class="row"><span class="key">Current Cycle</span><span class="val">{{ $cycleStart }}/{{ $cycleEnd }}</span></div>
                <div class="row"><span class="key">Next Reset</span><span class="val">September {{ $cycleEnd }}</span></div>
            </div>

            <button class="btn btn-reset">Reset All Staff Points</button>
        </div>

        <div class="card">
            <h2>Data Protection Details</h2>
            <p class="desc">Kenya Data Protection Act, 2019 compliance information.</p>

            <table class="dp-table">
                <tbody>
                    <tr><td>Data Controller</td><td>Strathmore University, Madaraka Estate, Nairobi</td></tr>
                    <tr><td>Applicable Law</td><td>Kenya Data Protection Act, 2019</td></tr>
                    <tr><td>Retention Period</td><td>1 academic year from event date</td></tr>
                    <tr><td>Contact</td><td>dataprotection@strathmore.edu</td></tr>
                    <tr><td>DPIA Status</td><td>Initial DPIA completed</td></tr>
                </tbody>
            </table>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:24px;">
                @csrf
                <button type="submit" class="btn btn-logout">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>