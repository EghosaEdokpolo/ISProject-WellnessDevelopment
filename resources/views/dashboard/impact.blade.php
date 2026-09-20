<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness Impact Score</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding: 0 4% 20px; }
        .kpi-card { background: #fff; border-radius: 16px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .kpi-label { color: #6a7e9a; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .kpi-value { font-size: 28px; font-weight: 700; color: #02338d; }
        .kpi-sub { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .filter-row { padding: 0 4% 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-btn { padding: 8px 18px; border-radius: 20px; border: 2px solid #ccc; background: #fff; cursor: pointer; font-weight: 600; font-size: 13px; }
        .filter-btn.active { background: #02338d; color: #fff; border-color: #02338d; }
        .table-wrap { padding: 0 4% 40px; }
        .data-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-collapse: collapse; }
        .data-table th { background: #02338d; color: #fff; padding: 12px; text-align: left; font-size: 13px; }
        .data-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .lift-positive { color: #0a7b2b; font-weight: 700; }
        .lift-zero { color: #888; }
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
                <a href="/hrdb_impact.html" class="nav-link active"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon" id="impact-icon"> Impact</a>
                <a href="/hrdb_retention.html" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                <a href="/hrdb_departmental.html" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Wellness Impact Score</h1>
    <p class="caption" style="padding-left:4%; color:#6b7280;">Based on the WHO-5 Well-Being Index. Score below 13 indicates poor wellbeing. Compares pre vs post per event.</p>

    <div id="errorBanner" class="error-banner"></div>

    <div class="filter-row">
        <button class="filter-btn active" data-cat="">All</button>
        <button class="filter-btn" data-cat="physical">Physical</button>
        <button class="filter-btn" data-cat="mental">Mental</button>
        <button class="filter-btn" data-cat="financial">Financial</button>
        <button class="filter-btn" data-cat="social">Social</button>
    </div>

    <p class="section-title">AVERAGE LIFT BY CATEGORY</p>
    <div class="kpi-grid" id="catGrid"></div>

    <p class="section-title">EVENT BREAKDOWN</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Event</th><th>Category</th><th>Pre Avg</th><th>Post Avg</th><th>Lift</th><th>Sample</th></tr>
            </thead>
            <tbody id="eventRows"></tbody>
        </table>
    </div>

    <script type="module">
        import { apiFetch, clearToken } from '/js/api.js';

        let currentCat = '';

        function card(label, value, sub) {
            return `<div class="kpi-card">
                <div class="kpi-label">${label}</div>
                <div class="kpi-value">${value}</div>
                <div class="kpi-sub">${sub}</div>
            </div>`;
        }

        async function load() {
            try {
                const q = currentCat ? `?category=${currentCat}` : '';
                const data = await apiFetch('/dashboard/impact' + q);

                const cats = data.categories || {};
                document.getElementById('catGrid').innerHTML = Object.entries(cats).map(([name, v]) =>
                    card(name, (v.avg_lift >= 0 ? '+' : '') + v.avg_lift, `${v.sample} responses`)
                ).join('');

                const rows = data.event_table || [];
                document.getElementById('eventRows').innerHTML = rows.length
                    ? rows.map(r => `<tr>
                        <td><strong>${r.event}</strong></td>
                        <td>${r.category}</td>
                        <td>${r.pre_average ?? '—'}</td>
                        <td>${r.post_average ?? '—'}</td>
                        <td class="${r.lift > 0 ? 'lift-positive' : 'lift-zero'}">${r.lift > 0 ? '+' : ''}${r.lift}</td>
                        <td>${r.follow_up ? '✔' : '—'}</td>
                    </tr>`).join('')
                    : '<tr><td colspan="6" style="text-align:center;color:#888;">No events in this category yet.</td></tr>';
            } catch (err) {
                const b = document.getElementById('errorBanner');
                b.textContent = 'Failed to load impact data: ' + err.message;
                b.style.display = 'block';
            }
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentCat = btn.dataset.cat;
                load();
            });
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