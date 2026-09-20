<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hr_view_style.css') }}">
    <style>
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; padding: 20px 4% 40px; }
        .card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card h2 { color: #02338d; margin: 0 0 16px; font-size: 17px; }
        .field { margin-bottom: 14px; }
        .field label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #333; }
        .field input, .field textarea, .field select { width: 100%; padding: 10px; border: 2px solid #ccc; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
        .radio-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .radio-row input { display: none; }
        .radio-row label { padding: 8px 16px; border: 2px solid #ccc; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .radio-row input:checked + label { background: #e24237; color: #fff; border-color: #e24237; }
        .btn { display: block; width: 100%; padding: 12px; font-size: 15px; font-weight: 700; background: #cca04a; color: #000; border: none; border-radius: 10px; cursor: pointer; margin-top: 12px; }
        .btn:active { background: #8b692f; }
        .btn-danger { background: #fee; color: #b00; border: 2px solid #b00; }
        .event-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-radius: 10px; margin-bottom: 8px; background: #f5f4f0; }
        .event-item.published { background: #e7f8ee; border-left: 4px solid #0a7b2b; }
        .event-item.draft { background: #fff8e7; border-left: 4px solid #a87600; }
        .event-title { font-weight: 700; color: #1f2937; font-size: 14px; }
        .event-meta { font-size: 12px; color: #6b7280; }
        .event-actions { display: flex; gap: 8px; }
        .mini-btn { padding: 6px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; border: none; font-weight: 600; }
        .mini-btn.publish { background: #0a7b2b; color: #fff; }
        .mini-btn.qr { background: #02338d; color: #fff; }
        .mini-btn.sent { background: #ddd; color: #666; }
        .qr-holder { text-align: center; padding: 20px; }
        .qr-holder img { max-width: 300px; border-radius: 12px; }
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
            <a href="/hrdb_eventsmanagement.html" class="nav-link active"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="/hrdb_settings.html" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            <a href="#" id="logoutBtn" class="nav-link" style="margin-left:auto; color:#b00;">Logout</a>
        </nav>
    </header>

    <h1>Manage Events</h1>

    <div id="errorBanner" class="error-banner"></div>
    <div id="successBanner" class="success-banner"></div>

    <div class="grid">
        <div class="card">
            <h2>Create & Publish Events</h2>
            <form id="createForm">
                <div class="field"><label>Event Name</label><input type="text" name="title" required></div>
                <div class="field"><label>Date</label><input type="datetime-local" name="starts_at" required></div>
                <div class="field"><label>Location</label><input type="text" name="venue" placeholder="Room/Venue" required></div>
                <div class="field">
                    <label>Wellness Category</label>
                    <div class="radio-row">
                        <input type="radio" name="wellness_category" id="physical" value="physical" required><label for="physical">Physical</label>
                        <input type="radio" name="wellness_category" id="mental" value="mental"><label for="mental">Mental</label>
                        <input type="radio" name="wellness_category" id="financial" value="financial"><label for="financial">Financial</label>
                        <input type="radio" name="wellness_category" id="social" value="social"><label for="social">Social</label>
                    </div>
                </div>
                <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <div class="field"><label>Points Awarded (on check-in)</label><input type="number" name="points_awarded" value="10" min="0"></div>
                <button class="btn" type="submit">Publish Event</button>
            </form>
        </div>

        <div class="card">
            <h2>QR Code</h2>
            <p style="color:#6b7280; font-size:13px;">Select an event below to view its check-in QR code.</p>
            <div class="qr-holder" id="qrHolder"><p style="color:#999;">No event selected.</p></div>
        </div>

        <div class="card">
            <h2>Upcoming Events</h2>
            <div id="eventsList"><p style="color:#888;">Loading…</p></div>
        </div>
    </div>

    <script type="module">
        import { apiFetch, clearToken, getToken } from '/js/api.js';

        function showError(m) { const b = document.getElementById('errorBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 6000); }
        function showSuccess(m) { const b = document.getElementById('successBanner'); b.textContent = m; b.style.display = 'block'; setTimeout(() => b.style.display = 'none', 4000); }

        async function loadEvents() {
            try {
                const data = await apiFetch('/events');
                const events = data.data || [];
                const list = document.getElementById('eventsList');

                if (!events.length) {
                    list.innerHTML = '<p style="color:#888;">No events yet. Create one on the left.</p>';
                    return;
                }

                list.innerHTML = events.map(e => `
                    <div class="event-item ${e.is_published ? 'published' : 'draft'}">
                        <div>
                            <div class="event-title">${e.title}</div>
                            <div class="event-meta">${new Date(e.starts_at).toLocaleString()} • ${e.venue ?? ''} • ${e.category_label}</div>
                        </div>
                        <div class="event-actions">
                            ${!e.is_published ? `<button class="mini-btn publish" data-publish="${e.id}">Publish</button>` : `<span class="mini-btn sent">Published</span>`}
                            <button class="mini-btn qr" data-qr="${e.id}" data-title="${e.title}">QR</button>
                        </div>
                    </div>
                `).join('');

                list.querySelectorAll('[data-publish]').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        try {
                            await apiFetch(`/events/${btn.dataset.publish}/publish`, { method: 'POST' });
                            showSuccess('Event published.');
                            loadEvents();
                        } catch (err) { showError(err.message); }
                    });
                });

                list.querySelectorAll('[data-qr]').forEach(btn => {
                    btn.addEventListener('click', () => loadQR(btn.dataset.qr, btn.dataset.title));
                });
            } catch (err) {
                showError('Failed to load events: ' + err.message);
            }
        }

        async function loadQR(id, title) {
            const holder = document.getElementById('qrHolder');
            holder.innerHTML = '<p style="color:#999;">Generating…</p>';
            try {
                const res = await fetch(`/api/v1/events/${id}/qr`, {
                    headers: { 'Authorization': `Bearer ${getToken()}` }
                });
                if (!res.ok) throw new Error('QR generation failed');
                const svg = await res.text();
                holder.innerHTML = `<h3 style="color:#02338d;margin-top:0;">${title}</h3>${svg}<p style="color:#6b7280;font-size:12px;">Scan to check in</p>`;
            } catch (err) {
                holder.innerHTML = `<p style="color:#b00;">${err.message}</p>`;
            }
        }

        document.getElementById('createForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const fd = new FormData(form);

            const payload = {
                title: fd.get('title'),
                starts_at: fd.get('starts_at').replace('T', ' ') + ':00',
                venue: fd.get('venue'),
                wellness_category: fd.get('wellness_category'),
                description: fd.get('description') || null,
                points_awarded: parseInt(fd.get('points_awarded')) || 10,
            };

            try {
                await apiFetch('/events', { method: 'POST', body: JSON.stringify(payload) });
                showSuccess('Event created.');
                form.reset();
                loadEvents();
            } catch (err) {
                showError('Create failed: ' + err.message);
            }
        });

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