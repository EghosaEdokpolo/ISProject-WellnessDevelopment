<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
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
        .error-banner { background: #fee; color: #a00; padding: 10px 16px; border-radius: 8px; margin: 20px 4%; display: none; }
        .success-banner { background: #e7f8ee; color: #0a7b2b; padding: 10px 16px; border-radius: 8px; margin: 20px 4%; display: none; }
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
                <a href="/hrdb_departmental.html" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link active"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Settings</h1>

    <div id="errorBanner" class="error-banner"></div>
    <div id="successBanner" class="success-banner"></div>

    <div class="grid">
        <div class="card">
            <h2>Points Reset Control</h2>
            <p class="desc">Points reset annually. This action is irreversible; all staff balances will be zeroed.</p>

            <div class="info-box">
                <div class="row"><span class="key">Current Cycle</span><span class="val" id="currentYear">—</span></div>
                <div class="row"><span class="key">Next Reset</span><span class="val" id="nextReset">—</span></div>
            </div>

            <button class="btn btn-reset" id="resetBtn">Reset All Staff Points</button>
        </div>

        <div class="card">
            <h2>Data Protection Details</h2>
            <p class="desc">Kenya Data Protection Act, 2019 compliance information.</p>

            <table class="dp-table">
                <tbody>
                    <tr><td>Data Controller</td><td id="dpController">—</td></tr>
                    <tr><td>Applicable Law</td><td id="dpLaw">—</td></tr>
                    <tr><td>Retention Period</td><td id="dpRetention">—</td></tr>
                    <tr><td>Contact</td><td id="dpContact">—</td></tr>
                    <tr><td>DPIA Status</td><td id="dpDpia">—</td></tr>
                </tbody>
            </table>

            <button class="btn btn-logout" id="logoutBtn" style="margin-top:24px;">Logout</button>
        </div>
    </div>

    <script type="module">
        import { apiFetch, clearToken } from '/js/api.js';

        function showError(m) { const b = document.getElementById('errorBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 6000); }
        function showSuccess(m) { const b = document.getElementById('successBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 5000); }

        async function load() {
            try {
                const d = await apiFetch('/settings');
                document.getElementById('currentYear').textContent = d.points.current_academic_year;
                document.getElementById('nextReset').textContent   = d.points.next_reset;
                document.getElementById('dpController').textContent = d.data_protection.controller;
                document.getElementById('dpLaw').textContent        = d.data_protection.applicable_law;
                document.getElementById('dpRetention').textContent  = d.data_protection.retention_period;
                document.getElementById('dpContact').textContent    = d.data_protection.contact;
                document.getElementById('dpDpia').textContent       = d.data_protection.dpia_status;
            } catch (err) {
                showError('Failed to load settings: ' + err.message);
            }
        }

        document.getElementById('resetBtn').addEventListener('click', async () => {
            if (!confirm('This will zero all staff points. Are you sure?')) return;
            try {
                const r = await apiFetch('/points/reset', { method: 'POST' });
                showSuccess(`Points reset. ${r.staff_affected} staff affected.`);
            } catch (err) {
                showError('Reset failed: ' + err.message);
            }
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