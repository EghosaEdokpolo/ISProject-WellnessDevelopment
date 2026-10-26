<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        .att-filters { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; padding: 0 4% 20px; }
        .att-filters select { padding: 10px 14px; border: 1.5px solid #d1d5db; border-radius: 10px; font-size: 14px; background: #fff; min-width: 240px; }
        .att-filters label { font-size: 13px; font-weight: 600; color: #374151; }
        .att-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; padding: 0 4% 24px; }
        .att-summary .stat { background: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .att-summary .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6a7e9a; font-weight: 700; }
        .att-summary .stat-value { font-size: 26px; font-weight: 700; color: #02338d; margin-top: 4px; }
        .att-table-wrap { padding: 0 4% 40px; overflow-x: auto; }
        .att-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.07); font-size: 13px; }
        .att-table th { background: #02338d; color: #fff; padding: 13px 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; white-space: nowrap; }
        .att-table td { padding: 12px; border-bottom: 1px solid #eef0f3; color: #374151; }
        .att-table tr:last-child td { border-bottom: none; }
        .att-table tr:nth-child(even) { background: #fafbfc; }
        .att-name { font-weight: 700; color: #1f2937; white-space: nowrap; }
        .att-dept { color: #6b7280; font-size: 12px; }
        .att-center { text-align: center; }
        .att-num { font-weight: 700; color: #02338d; text-align: center; }
        .att-pill { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .pill-completed { background: #d4f4dd; color: #0a7b2b; }
        .pill-checked   { background: #dbe6ff; color: #02338d; }
        .pill-registered{ background: #fff4cf; color: #a87600; }
        .att-empty { text-align: center; padding: 60px 20px; color: #94a3b8; font-size: 14px; }
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
            <a href="{{ route('attendees.index') }}" class="nav-link active"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav>
    </header>

    <main>
        <h1>Attendee Table</h1>
        <p class="caption" style="padding-left:4%; color:#6b7280; margin-bottom:20px;">Full record of staff participation across all wellness events.</p>

        @php
            $eventOptions = $registrations->pluck('event')->unique('id')->sortBy('name');
            $deptOptions  = $registrations->pluck('user.department')->filter()->unique()->sort();
            $totalReg     = $registrations->count();
            $checkedIn    = $registrations->filter(fn($r) => $r->checkIn)->count();
            $completed    = $registrations->where('status', 'completed')->count();
            $withFeedback = $registrations->filter(fn($r) => $r->feedback)->count();
        @endphp

        <div class="att-summary">
            <div class="stat"><div class="stat-label">Total Registrations</div><div class="stat-value">{{ $totalReg }}</div></div>
            <div class="stat"><div class="stat-label">Checked In</div><div class="stat-value">{{ $checkedIn }}</div></div>
            <div class="stat"><div class="stat-label">Completed</div><div class="stat-value">{{ $completed }}</div></div>
            <div class="stat"><div class="stat-label">Feedback Given</div><div class="stat-value">{{ $withFeedback }}</div></div>
        </div>

        <div class="att-filters">
            <div><label for="eventFilter">Event</label><br><select id="eventFilter"><option value="">All events</option>@foreach($eventOptions as $ev)<option value="{{ $ev->id }}">{{ $ev->name }}</option>@endforeach</select></div>
            <div><label for="deptFilter">Department</label><br><select id="deptFilter"><option value="">All departments</option>@foreach($deptOptions as $dept)<option value="{{ $dept }}">{{ $dept }}</option>@endforeach</select></div>
            <div><label for="statusFilter">Status</label><br><select id="statusFilter"><option value="">All statuses</option><option value="registered">Registered</option><option value="checked_in">Checked In</option><option value="completed">Completed</option></select></div>
        </div>

        <div class="att-table-wrap">
            <table class="att-table">
                <thead>
                    <tr><th>Name</th><th>Department</th><th>Event</th><th class="att-center">Attended</th><th class="att-center">Consent</th><th class="att-center">Feedback</th><th class="att-center">WHO-5 Pre</th><th class="att-center">WHO-5 Post</th><th class="att-center">Lift</th><th class="att-center">Points</th><th>Status</th></tr>
                </thead>
                <tbody id="attendeeRows">
                    @forelse($registrations as $reg)
                        @php
                            $pre  = $reg->wellbeingResponses->firstWhere('stage', 'pre');
                            $post = $reg->wellbeingResponses->firstWhere('stage', 'post');
                            $lift = ($pre && $post) ? ($post->total_score - $pre->total_score) : null;
                            $statusClass = match($reg->status) { 'completed' => 'pill-completed', 'checked_in' => 'pill-checked', default => 'pill-registered' };
                        @endphp
                        <tr data-event="{{ $reg->event_id }}" data-dept="{{ $reg->user->department ?? '' }}" data-status="{{ $reg->status }}">
                            <td class="att-name">{{ $reg->user->name }}</td>
                            <td class="att-dept">{{ $reg->user->department ?? '—' }}</td>
                            <td class="att-dept">{{ $reg->event->name }}</td>
                            <td class="att-center">{!! $reg->checkIn ? '<span style="color:#0a7b2b; font-size:16px;">✓</span>' : '<span style="color:#cbd5e1;">—</span>' !!}</td>
                            <td class="att-center">{!! $reg->waiver ? '<span style="color:#0a7b2b; font-size:16px;">✓</span>' : '<span style="color:#cbd5e1;">—</span>' !!}</td>
                            <td class="att-center">{!! $reg->feedback ? '<span style="color:#0a7b2b; font-size:16px;">✓</span>' : '<span style="color:#cbd5e1;">—</span>' !!}</td>
                            <td class="att-num">{{ $pre->total_score ?? '—' }}</td>
                            <td class="att-num">{{ $post->total_score ?? '—' }}</td>
                            <td class="att-center">{!! $lift === null ? '<span style="color:#94a3b8;">—</span>' : ($lift > 0 ? "<span style='color:#0a7b2b;font-weight:700;'>+$lift</span>" : "<span style='color:#b91c1c;font-weight:700;'>$lift</span>") !!}</td>
                            <td class="att-num">{{ $reg->user->points_balance }}</td>
                            <td><span class="att-pill {{ $statusClass }}">{{ str_replace('_', ' ', $reg->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="att-empty">No registrations found yet.<br>When staff register for wellness events, they'll appear here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        (function () {
            const eventFilter  = document.getElementById('eventFilter');
            const deptFilter   = document.getElementById('deptFilter');
            const statusFilter = document.getElementById('statusFilter');
            const rows         = document.querySelectorAll('#attendeeRows tr[data-event]');

            function applyFilters() {
                const ev  = eventFilter.value;
                const dep = deptFilter.value;
                const st  = statusFilter.value;
                rows.forEach(row => {
                    const show = (!ev || row.dataset.event === ev) && (!dep || row.dataset.dept === dep) && (!st || row.dataset.status === st);
                    row.style.display = show ? '' : 'none';
                });
            }
            eventFilter.addEventListener('change', applyFilters);
            deptFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
        })();
    </script>
</body>
</html>