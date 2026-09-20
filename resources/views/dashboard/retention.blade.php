<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sustained Participation Rate</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .big-kpi { margin: 20px 4%; background: linear-gradient(135deg,#02338d,#0a56c4); color: #fff; padding: 40px; border-radius: 20px; text-align: center; }
        .big-kpi .number { font-size: 72px; font-weight: 800; line-height: 1; }
        .big-kpi .label { font-size: 16px; opacity: 0.9; margin-top: 10px; }
        .big-kpi .sub { font-size: 13px; opacity: 0.75; margin-top: 6px; }
        .sub-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; padding: 0 4% 40px; }
        .sub-kpi { background: #fff; padding: 20px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .sub-kpi .num { font-size: 32px; font-weight: 700; color: #02338d; }
        .sub-kpi .lab { color: #6a7e9a; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info { padding: 0 4% 20px; color: #555; font-size: 14px; }
        .error-banner { background: #fee; color: #a00; padding: 10px 16px; border-radius: 8px; margin: 20px 4%; display: none; }
    </style>
</head>
<body>
    <div class="header-title">
        <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
        Strathmore University - Wellness Programme Management & Impact System (WPMIS)
    </div>
    <header class="nav-head">
        <nav class="nav-menu">
            <div class="pillgroup1">
                <span class="category-label">Dashboards</span>
                <a href="/hrdb_overview.html" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
                <a href="/hrdb_impact.html" class="nav-link"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon" id="impact-icon"> Impact</a>
                <a href="/hrdb_retention.html" class="nav-link active"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="/hrdb_departmental.html" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Sustained Participation Rate</h1>
    <p class="info">% of staff who attend more than one wellness event per semester. Proves lasting appeal, not just novelty.</p>

    <div id="errorBanner" class="error-banner"></div>

    <div class="big-kpi">
        <div class="number" id="rate">—</div>
        <div class="label">Sustained Participation Rate</div>
        <div class="sub" id="period"></div>
    </div>

    <div class="sub-kpis">
        <div class="sub-kpi">
            <div class="lab">Active Staff</div>
            <div class="num" id="totalStaff">—</div>
        </div>
        <div class="sub-kpi">
            <div class="lab">Sustained Attendees</div>
            <div class="num" id="sustained">—</div>
        </div>
    </div>

    <script type="module">
        import { apiFetch, clearToken } from '/js/api.js';

        async function load() {
            try {
                const d = await apiFetch('/dashboard/retention');
                document.getElementById('rate').textContent = d.sustained_participation + '%';
                document.getElementById('totalStaff').textContent = d.total_active_staff;
                document.getElementById('sustained').textContent = d.sustained_attendees;
                document.getElementById('period').textContent = `${d.period.start} → ${d.period.end}`;
            } catch (err) {
                const b = document.getElementById('errorBanner');
                b.textContent = 'Failed to load retention data: ' + err.message;
                b.style.display = 'block';
            }
        }

        document.getElementById('logoutBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            try { await apiFetch('/auth/logout', { method: 'POST' }); } catch (_) {}
            clearToken();
            window.location.href = '/';
        });

        load();
    </script>
</body>
</html>