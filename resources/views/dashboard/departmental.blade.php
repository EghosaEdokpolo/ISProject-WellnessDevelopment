<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departmental Engagement</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding: 0 4% 20px; }
        .kpi-card { background: #fff; border-radius: 16px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .kpi-label { color: #6a7e9a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-value { font-size: 28px; font-weight: 700; color: #02338d; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 0 4% 20px; }
        @media (max-width: 700px) { .two-col { grid-template-columns: 1fr; } }
        .col-card { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .col-card h3 { color: #02338d; margin: 0 0 14px; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        .dept-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .dept-row:last-child { border-bottom: none; }
        .dept-name { color: #1f2937; font-weight: 600; font-size: 14px; }
        .dept-rate { font-weight: 700; color: #02338d; }
        .pill-engaged { background: #d4f4dd; color: #0a7b2b; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .pill-developing { background: #fff4cf; color: #a87600; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .pill-disengaged { background: #ffe0e0; color: #b00; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .table-wrap { padding: 0 4% 40px; }
        .data-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-collapse: collapse; }
        .data-table th { background: #02338d; color: #fff; padding: 12px; text-align: left; font-size: 13px; }
        .data-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .export-btn { display: inline-block; margin: 0 4% 20px; padding: 10px 22px; background: #cca04a; color: #000; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; }
        .section-title { padding: 0 4%; margin-top: 20px; color: #02338d; font-size: 18px; font-weight: 700; }
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
                <a href="/hrdb_retention.html" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="/hrdb_departmental.html" class="nav-link active"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Departmental Engagement Growth</h1>
    <p style="padding:0 4%; color:#6b7280; font-size:14px;">Analysis of departments across the semester.</p>

    <div id="errorBanner" class="error-banner"></div>

    <p class="section-title">OVERVIEW</p>
    <div class="kpi-grid" id="kpiGrid"></div>

    <div class="two-col">
        <div class="col-card">
            <h3>Most Active — Top 5</h3>
            <div id="topList"></div>
        </div>
        <div class="col-card">
            <h3>Needs Attention — Bottom 5</h3>
            <div id="bottomList"></div>
        </div>
    </div>

    <p class="section-title">RANKED TABLE</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Department</th><th>Code</th><th>Staff</th><th>Attendees</th><th>Rate</th><th>Status</th></tr>
            </thead>
            <tbody id="tableRows"></tbody>
        </table>
    </div>

    <a href="/api/v1/reports/departmental-export" class="export-btn" id="exportBtn">⬇ Export Report (CSV)</a>

    <script type="module">
        import { apiFetch, clearToken, getToken } from '/js/api.js';

        function statusPill(status) {
            const cls = status === 'engaged' ? 'pill-engaged' : status === 'developing' ? 'pill-developing' : 'pill-disengaged';
            return `<span class="${cls}">${status}</span>`;
        }

        function rowHTML(r) {
            return `<div class="dept-row">
                <span class="dept-name">${r.department}</span>
                <span class="dept-rate">${r.engagement_rate}%</span>
            </div>`;
        }

        async function load() {
            try {
                const d = await apiFetch('/dashboard/departmental');

                const all = d.all_departments || [];
                const avgRate = all.length ? (all.reduce((s, x) => s + x.engagement_rate, 0) / all.length).toFixed(1) : 0;
                const engaged = all.filter(x => x.status === 'engaged').length;

                document.getElementById('kpiGrid').innerHTML = `
                    <div class="kpi-card"><div class="kpi-label">Departments Tracked</div><div class="kpi-value">${all.length}</div></div>
                    <div class="kpi-card"><div class="kpi-label">Org-Wide Average Rate</div><div class="kpi-value">${avgRate}%</div></div>
                    <div class="kpi-card"><div class="kpi-label">Engaged Departments</div><div class="kpi-value">${engaged}</div></div>
                `;

                document.getElementById('topList').innerHTML    = d.most_active.length    ? d.most_active.map(rowHTML).join('')    : '<p style="color:#888;font-size:13px;">No data yet.</p>';
                document.getElementById('bottomList').innerHTML = d.needs_attention.length ? d.needs_attention.map(rowHTML).join('') : '<p style="color:#888;font-size:13px;">No data yet.</p>';

                document.getElementById('tableRows').innerHTML = all.map((r, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td><strong>${r.department}</strong></td>
                        <td>${r.code}</td>
                        <td>${r.total_staff}</td>
                        <td>${r.unique_attendees}</td>
                        <td><strong>${r.engagement_rate}%</strong></td>
                        <td>${statusPill(r.status)}</td>
                    </tr>
                `).join('');
            } catch (err) {
                const b = document.getElementById('errorBanner');
                b.textContent = 'Failed to load departmental data: ' + err.message;
                b.style.display = 'block';
            }
        }

        document.getElementById('exportBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            const res = await fetch('/api/v1/reports/departmental-export', {
                headers: { 'Authorization': `Bearer ${getToken()}` }
            });
            if (!res.ok) { alert('Export failed'); return; }
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'departmental-engagement.csv'; a.click();
            URL.revokeObjectURL(url);
        });

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