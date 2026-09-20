<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="group 1">
    <meta name="description" content="HR / People and Culture analytical dashboard — staff wellness metrics.">
    <title>Overview Dashboard</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; padding: 0 4% 20px; }
        .kpi-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .kpi-label { color: #6a7e9a; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .kpi-value { font-size: 32px; font-weight: 700; color: #02338d; }
        .kpi-sub { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .section-title { padding: 0 4%; margin-top: 20px; color: #02338d; font-size: 18px; font-weight: 700; }
        .dept-list { padding: 0 4%; margin-bottom: 40px; }
        .dept-row { display: flex; justify-content: space-between; padding: 10px 16px; background: #fff; margin-bottom: 8px; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
        .dept-name { font-weight: 600; color: #1f2937; }
        .dept-rate { font-weight: 700; color: #02338d; }
        .error-banner { background: #fee; color: #a00; padding: 10px 16px; border-radius: 8px; margin: 20px 4%; display: none; }
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
                <a href="/hrdb_overview.html" class="nav-link active">
                    <img src="{{ asset('images/analytics-icon.png') }}" alt="" class="nav-icon"> Overview
                </a>
                <a href="/hrdb_impact.html" class="nav-link">
                    <img src="{{ asset('images/Impact-icon.png') }}" alt="" class="nav-icon" id="impact-icon"> Impact
                </a>
                <a href="/hrdb_retention.html" class="nav-link">
                    <img src="{{ asset('images/retention-icon1.png') }}" alt="" class="nav-icon" id="retention-icon"> Retention
                </a>
                <a href="/hrdb_departmental.html" class="nav-link">
                    <img src="{{ asset('images/department-icon.png') }}" alt="" class="nav-icon"> Departmental
                </a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link">
                <img src="{{ asset('images/attendance-icon.png') }}" alt="" class="nav-icon"> Attendees
            </a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link">
                <img src="{{ asset('images/events-icon.png') }}" alt="" class="nav-icon"> Events
            </a>
            <a href="/hrdb_settings.html" class="nav-link">
                <img src="{{ asset('images/settings-icon.png') }}" alt="" class="nav-icon"> Settings
            </a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Overview Dashboard</h1>

    <div id="errorBanner" class="error-banner"></div>

    <p class="section-title">LEADING INDICATORS — SYSTEM ADOPTION</p>
    <div class="kpi-grid" id="leadingGrid"></div>

    <p class="section-title">LAGGING INDICATORS — WELLNESS OUTCOMES</p>
    <div class="kpi-grid" id="laggingGrid"></div>

    <p class="section-title">DEPARTMENTAL SNAPSHOT</p>
    <div class="dept-list" id="deptList"></div>

    <script type="module">
        import { apiFetch, clearToken } from '/js/api.js';

        function kpi(label, value, sub) {
            return `<div class="kpi-card">
                <div class="kpi-label">${label}</div>
                <div class="kpi-value">${value}</div>
                <div class="kpi-sub">${sub || ''}</div>
            </div>`;
        }

        function deptRow(name, rate) {
            return `<div class="dept-row">
                <span class="dept-name">${name}</span>
                <span class="dept-rate">${rate}%</span>
            </div>`;
        }

        async function load() {
            try {
                const data = await apiFetch('/dashboard/overview');

                document.getElementById('leadingGrid').innerHTML =
                    kpi('Events Published',    data.leading.events_published,    'active this year') +
                    kpi('Total Registrations', data.leading.total_registrations, 'event sign-ups') +
                    kpi('Staff With Consent',  data.leading.staff_with_consent,  'data consent given') +
                    kpi('System Adoption',     data.leading.system_adoption_pct + '%', 'staff interacting');

                document.getElementById('laggingGrid').innerHTML =
                    kpi('Average Impact Lift',  '+' + data.lagging.avg_impact_lift, 'WHO-5 points') +
                    kpi('Sustained Participation', data.lagging.sustained_participation_pct + '%', 'attend >1 event') +
                    kpi('Avg Points / Staff',   data.lagging.avg_points_per_staff, 'gamification');

                const top    = data.departmental_snapshot.top    || [];
                const bottom = data.departmental_snapshot.bottom || [];

                let html = '';
                if (top.length) {
                    html += '<p style="color:#6a7e9a;font-size:13px;font-weight:700;margin:0 0 8px;">Most Active</p>';
                    html += top.map(d => deptRow(d.department, d.engagement_rate)).join('');
                }
                if (bottom.length) {
                    html += '<p style="color:#6a7e9a;font-size:13px;font-weight:700;margin:16px 0 8px;">Needs Attention</p>';
                    html += bottom.map(d => deptRow(d.department, d.engagement_rate)).join('');
                }
                document.getElementById('deptList').innerHTML = html || '<p style="color:#888;">No attendance data yet.</p>';
            } catch (err) {
                const b = document.getElementById('errorBanner');
                b.textContent = 'Failed to load dashboard: ' + err.message;
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