<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendees</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .selector { padding: 0 4% 20px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .selector select { padding: 10px 16px; border-radius: 10px; border: 2px solid #ccc; font-size: 14px; min-width: 320px; }
        .table-wrap { padding: 0 4% 40px; }
        .data-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-collapse: collapse; }
        .data-table th { background: #02338d; color: #fff; padding: 12px; text-align: left; font-size: 13px; }
        .data-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .status { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-completed { background: #d4f4dd; color: #0a7b2b; }
        .status-pending { background: #fff4cf; color: #a87600; }
        .status-registered { background: #dbe6ff; color: #02338d; }
        .empty { padding: 40px; text-align: center; color: #888; background: #fff; border-radius: 12px; }
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
                <a href="/hrdb_departmental.html" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="/hrdb_attendeetable.html" class="nav-link active"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="/hrdb_eventsmanagement.html" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Attendee Table</h1>
    <p class="caption" style="padding-left:4%; color:#6b7280;">Full record of staff participation per event.</p>

    <div id="errorBanner" class="error-banner"></div>

    <div class="selector">
        <label style="font-weight:600;">Select Event:</label>
        <select id="eventSelect"><option>Loading…</option></select>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="attendeeTable" style="display:none;">
            <thead>
                <tr>
                    <th>Name</th><th>Department</th><th>Attended</th><th>Consent</th>
                    <th>Feedback</th><th>WHO-5 Pre</th><th>WHO-5 Post</th><th>Points</th><th>Status</th>
                </tr>
            </thead>
            <tbody id="attendeeRows"></tbody>
        </table>
        <div class="empty" id="emptyMsg">Select an event to view attendees.</div>
    </div>

    <script type="module">
        import { apiFetch, clearToken } from '/js/api.js';

        const select = document.getElementById('eventSelect');
        const table = document.getElementById('attendeeTable');
        const rowsEl = document.getElementById('attendeeRows');
        const emptyEl = document.getElementById('emptyMsg');

        function boolIcon(v) { return v ? '✅' : '—'; }

        function statusPill(s) {
            return `<span class="status status-${s}">${s}</span>`;
        }

        async function loadEvents() {
            try {
                const data = await apiFetch('/events');
                const events = data.data || [];
                if (!events.length) {
                    select.innerHTML = '<option>No events yet</option>';
                    return;
                }
                select.innerHTML = events.map(e =>
                    `<option value="${e.id}">${e.title} — ${new Date(e.starts_at).toLocaleDateString()}</option>`
                ).join('');
                loadAttendees(events[0].id);
            } catch (err) {
                showError('Failed to load events: ' + err.message);
            }
        }

        async function loadAttendees(eventId) {
            try {
                const data = await apiFetch(`/events/${eventId}/attendees`);
                const rows = Array.isArray(data) ? data : (data.data || []);

                if (!rows.length) {
                    table.style.display = 'none';
                    emptyEl.style.display = 'block';
                    emptyEl.textContent = 'No attendees registered for this event yet.';
                    return;
                }

                emptyEl.style.display = 'none';
                table.style.display = 'table';
                rowsEl.innerHTML = rows.map(r => `<tr>
                    <td><strong>${r.name ?? '—'}</strong></td>
                    <td>${r.department ?? '—'}</td>
                    <td>${boolIcon(r.attended)}</td>
                    <td>${boolIcon(r.consent)}</td>
                    <td>${boolIcon(r.feedback_provided)}</td>
                    <td>${r.who5_pre ?? '—'}</td>
                    <td>${r.who5_post ?? '—'}</td>
                    <td><strong>${r.points ?? 0}</strong></td>
                    <td>${statusPill(r.status)}</td>
                </tr>`).join('');
            } catch (err) {
                showError('Failed to load attendees: ' + err.message);
            }
        }

        function showError(msg) {
            const b = document.getElementById('errorBanner');
            b.textContent = msg;
            b.style.display = 'block';
        }

        select.addEventListener('change', () => loadAttendees(select.value));

        document.getElementById('logoutBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            try { await apiFetch('/auth/logout', { method: 'POST' }); } catch (_) {}
            clearToken();
            window.location.href = '/';
        });

        loadEvents();
    </script>
</body>
</html>