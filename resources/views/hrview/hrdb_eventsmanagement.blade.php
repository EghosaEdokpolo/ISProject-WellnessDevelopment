<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .ev-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 24px; padding: 20px 4% 40px; align-items: start; }
        .ev-card { background: #ffffff; border-radius: 16px; padding: 22px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); display: flex; flex-direction: column; min-height: 440px; max-height: 560px; box-sizing: border-box; overflow: hidden; }
        .ev-card h2 { color: #02338d; font-size: 16px; font-weight: 700; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 0.4px; }
        .ev-card p.ev-desc { color: #6b7280; font-size: 13px; margin: 0 0 16px; }
        .ev-field { margin-bottom: 12px; }
        .ev-field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .ev-field input[type="text"], .ev-field input[type="datetime-local"], .ev-field input[type="number"], .ev-field textarea, .ev-field select { width: 100%; padding: 10px 12px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box; background: #fafafa; transition: border-color 0.15s, background 0.15s; }
        .ev-field input:focus, .ev-field textarea:focus, .ev-field select:focus { outline: none; border-color: #02338d; background: #fff; }
        .ev-cats { display: flex; gap: 8px; flex-wrap: wrap; }
        .ev-cats input[type="radio"] { display: none; }
        .ev-cats label { padding: 7px 16px; border: 1.5px solid #d1d5db; border-radius: 20px; font-size: 12px; font-weight: 600; color: #4b5563; cursor: pointer; transition: all 0.15s; margin: 0; }
        .ev-cats input[type="radio"]:checked + label { background: #02338d; border-color: #02338d; color: #fff; }
        .ev-btn { display: block; width: 100%; padding: 12px; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: opacity 0.15s; margin-top: auto; }
        .ev-btn:active { opacity: 0.85; }
        .ev-btn:disabled { background: #cbd5e1; color: #94a3b8; cursor: not-allowed; }
        .ev-btn-primary { background: #cca04a; color: #000; }
        .ev-btn-secondary { background: #02338d; color: #fff; }
        .ev-qr-frame { flex: 1; display: flex; align-items: center; justify-content: center; background: #f7f8fa; border-radius: 12px; padding: 16px; margin: 8px 0 16px; overflow: hidden; min-height: 280px; max-height: 320px; }
        .ev-qr-frame .placeholder { color: #94a3b8; font-size: 13px; text-align: center; padding: 20px; }
        .ev-qr-frame svg { max-width: 100%; max-height: 100%; height: auto; width: auto; display: block; }
        .ev-list { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-right: 6px; max-height: 380px; }
        .ev-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; border-radius: 10px; background: #f7f8fa; border: 1px solid #eef0f3; gap: 12px; }
        .ev-item-info { flex: 1; min-width: 0; }
        .ev-item-title { font-weight: 700; color: #1f2937; font-size: 14px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ev-item-meta { font-size: 12px; color: #6b7280; margin-top: 3px; display: block; }
        .ev-item-btn { padding: 6px 14px; background: #02338d; color: #fff; border: none; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.3px; cursor: pointer; flex-shrink: 0; }
        .ev-item-btn:hover { background: #0a4ba8; }
        .ev-preview { flex: 1; background: #f7f8fa; border-radius: 12px; padding: 16px; margin: 8px 0 16px; overflow-y: auto; min-height: 160px; font-size: 13px; line-height: 1.7; color: #374151; }
        .ev-preview strong { color: #02338d; }
        .ev-flash { margin: 0 4% 16px; padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 500; }
        .ev-flash-ok  { background: #e7f8ee; color: #0a7b2b; border-left: 4px solid #0a7b2b; }
        .ev-flash-err { background: #fef0f0; color: #b91c1c; border-left: 4px solid #b91c1c; }
        .ev-toast { position: fixed; top: 20px; right: 20px; padding: 14px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 9999; display: none; max-width: 320px; }
        .ev-toast-ok  { background: #0a7b2b; color: #fff; }
        .ev-toast-err { background: #b91c1c; color: #fff; }
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
            <a href="{{ route('events.management') }}" class="nav-link active"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <h1>Manage Events</h1>

    @if(session('success'))
        <div class="ev-flash ev-flash-ok">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="ev-flash ev-flash-err">{{ $errors->first() }}</div>
    @endif

    <div id="toast" class="ev-toast"></div>

    <main>
        <div class="ev-grid">
            <!-- Create Event -->
            <div class="ev-card">
                <h2>Create & Publish Events</h2>
                <p class="ev-desc">Add a new wellness event to the programme.</p>
                <form method="POST" action="{{ route('events.store') }}" style="display:flex; flex-direction:column; flex:1;">
                    @csrf
                    <div class="ev-field"><label>Event Name</label><input type="text" name="name" value="{{ old('name') }}" required maxlength="255"></div>
                    <div class="ev-field"><label>Date & Time</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required></div>
                    <div class="ev-field"><label>Location</label><input type="text" name="location" value="{{ old('location') }}" required placeholder="Room / Venue" maxlength="255"></div>
                    <div class="ev-field"><label>Capacity</label><input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required></div>
                    <div class="ev-field">
                        <label>Wellness Category</label>
                        <div class="ev-cats">
                            <input type="radio" name="category" id="cat-physical" value="physical" {{ old('category') === 'physical' ? 'checked' : '' }}><label for="cat-physical">Physical</label>
                            <input type="radio" name="category" id="cat-mental" value="mental" {{ old('category', 'mental') === 'mental' ? 'checked' : '' }}><label for="cat-mental">Mental</label>
                            <input type="radio" name="category" id="cat-financial" value="financial" {{ old('category') === 'financial' ? 'checked' : '' }}><label for="cat-financial">Financial</label>
                            <input type="radio" name="category" id="cat-social" value="social" {{ old('category') === 'social' ? 'checked' : '' }}><label for="cat-social">Social</label>
                        </div>
                    </div>
                    <div class="ev-field"><label>Description</label><textarea name="description" rows="3" required style="resize:none;"></textarea></div>
                    <button type="submit" class="ev-btn ev-btn-primary">Publish Event</button>
                </form>
            </div>

            <!-- QR Code -->
            <div class="ev-card">
                <h2>Event QR Code</h2>
                <p class="ev-desc">Click <strong>QR</strong> next to an event to generate its check-in code.</p>
                <div class="ev-qr-frame" id="qrFrame">
                    <div class="placeholder" id="qrPlaceholder">No QR generated yet</div>
                    <div id="qrHolder" style="display:none;"></div>
                </div>
                <div id="qrEventName" style="text-align:center; font-weight:700; color:#02338d; font-size:13px; margin-bottom:8px; min-height:18px;"></div>
                <button type="button" id="downloadQrBtn" disabled class="ev-btn ev-btn-secondary">Download QR Code</button>
            </div>

            <!-- Upcoming Events -->
            <div class="ev-card">
                <h2>Upcoming Events</h2>
                <p class="ev-desc">All published wellness events. Click QR to generate check-in code.</p>
                <div class="ev-list">
                    @forelse($events as $event)
                        <div class="ev-item">
                            <div class="ev-item-info">
                                <span class="ev-item-title">{{ $event->name }}</span>
                                <span class="ev-item-meta">{{ $event->starts_at->format('D, d M Y • H:i') }} — {{ $event->location }}</span>
                            </div>
                            <button type="button" class="ev-item-btn qr-btn" data-qr-id="{{ $event->id }}" data-qr-name="{{ $event->name }}">QR</button>
                        </div>
                    @empty
                        <div style="text-align:center; padding:40px 20px; color:#94a3b8; font-size:13px;">No events yet.<br>Create one to get started.</div>
                    @endforelse
                </div>
            </div>

            <!-- Send Awareness -->
            <div class="ev-card">
                <h2>Send Awareness Communication</h2>
                <p class="ev-desc">Email invite to selected staff. Emails log to storage/logs/laravel.log.</p>
                <div class="ev-field">
                    <label for="eventcategory">Select Event</label>
                    <select id="eventcategory"><option value="">— Choose an event —</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select>
                </div>
                <div class="ev-field">
                    <label for="emailcategory">Invitee Category</label>
                    <select id="emailcategory"><option value="all">All staff</option><option value="female">Female staff</option><option value="male">Male staff</option><option value="cafeteria">Cafeteria staff</option><option value="housekeeping">Housekeeping staff</option><option value="sces">SCES</option><option value="sth">STH</option></select>
                </div>
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Event Preview</label>
                <div class="ev-preview" id="eventPreview"><span style="color:#94a3b8;">Select an event above to preview its details.</span></div>
                <button type="button" id="sendCommBtn" class="ev-btn ev-btn-secondary">Send Email + Calendar Invite</button>
            </div>
        </div>
    </main>

    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const toast = document.getElementById('toast');
            function showToast(message, type) {
                toast.textContent = message;
                toast.className = 'ev-toast ' + (type === 'ok' ? 'ev-toast-ok' : 'ev-toast-err');
                toast.style.display = 'block';
                setTimeout(() => { toast.style.display = 'none'; }, 4000);
            }
            const qrPlaceholder = document.getElementById('qrPlaceholder');
            const qrHolder = document.getElementById('qrHolder');
            const qrEventName = document.getElementById('qrEventName');
            const downloadBtn = document.getElementById('downloadQrBtn');
            let currentSvg = null, currentQrId = null;

            const eventData = {
                @foreach($events as $event)
                    "{{ $event->id }}": {
                        name: @json($event->name), date: "{{ $event->starts_at->format('D, d M Y • H:i') }}",
                        location: @json($event->location), category: "{{ ucfirst($event->category) }}", capacity: "{{ $event->capacity }}", desc: @json($event->description),
                    },
                @endforeach
            };

            document.querySelectorAll('.qr-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.qrId, name = btn.dataset.qrName;
                    qrPlaceholder.style.display = 'none'; qrHolder.style.display = 'block'; qrHolder.innerHTML = '<span style="color:#94a3b8; font-size:13px;">Generating…</span>'; qrEventName.textContent = name; downloadBtn.disabled = true;
                    try {
                        const res = await fetch(`/events/${id}/qr`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Server returned ' + res.status);
                        const svgText = await res.text();
                        qrHolder.innerHTML = svgText; currentSvg = svgText; currentQrId = id; downloadBtn.disabled = false;
                    } catch (err) { qrHolder.innerHTML = `<span style="color:#b91c1c; font-size:13px;">Failed: ${err.message}</span>`; }
                });
            });

            downloadBtn.addEventListener('click', () => {
                if (!currentSvg || !currentQrId) return;
                const blob = new Blob([currentSvg], { type: 'image/svg+xml' });
                const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = `event-${currentQrId}-qr.svg`; a.click(); URL.revokeObjectURL(url);
            });

            document.getElementById('eventcategory').addEventListener('change', (e) => {
                const id = e.target.value, preview = document.getElementById('eventPreview');
                if (!id || !eventData[id]) { preview.innerHTML = '<span style="color:#94a3b8;">Select an event above to preview its details.</span>'; return; }
                const d = eventData[id];
                preview.innerHTML = `<strong>${d.name}</strong><br><span style="color:#6b7280;">📅</span> ${d.date}<br><span style="color:#6b7280;">📍</span> ${d.location}<br><span style="color:#6b7280;">🏷</span> ${d.category} &nbsp;·&nbsp; <span style="color:#6b7280;">👥</span> Capacity: ${d.capacity}${d.desc ? `<div style="margin-top:10px; padding-top:10px; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px;">${d.desc}</div>` : ''}`;
            });

            const sendBtn = document.getElementById('sendCommBtn');
            sendBtn.addEventListener('click', async () => {
                const eventId = document.getElementById('eventcategory').value, recipient = document.getElementById('emailcategory').value;
                if (!eventId) { showToast('Pick an event first.', 'err'); return; }
                sendBtn.disabled = true; const originalText = sendBtn.textContent; sendBtn.textContent = 'Sending…';
                try {
                    const res = await fetch('{{ route('communications.send') }}', {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin',
                        body: JSON.stringify({ event_id: eventId, recipient: recipient }),
                    });
                    const data = await res.json(); if (!res.ok) throw new Error(data.message || 'Send failed');
                    showToast(data.message, 'ok');
                } catch (err) { showToast('Failed: ' + err.message, 'err'); } finally { sendBtn.disabled = false; sendBtn.textContent = originalText; }
            });
        })();
    </script>
</body>
</html>