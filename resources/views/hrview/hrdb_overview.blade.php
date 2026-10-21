<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Overview Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    <style>
        body { background: #f5f4f0; font-family: 'Lato', sans-serif; }
        .kpi-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 4% 20px; }
        .kpi-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; padding: 0 4% 20px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; display: flex; justify-content: space-between; }
        .metric-value { font-size: 32px; font-weight: 800; color: #02338d; margin: 10px 0 4px; }
        .metric-sub { font-size: 12px; color: #9ca3af; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-leading { background: #dcfce7; color: #166534; }
        .badge-lagging { background: #fee2e2; color: #991b1b; }
        .split-row { padding: 0 4% 10px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .activity-list { display: flex; flex-direction: column; gap: 12px; margin-top: 15px; }
        .activity-item { display: flex; gap: 12px; font-size: 13px; align-items: flex-start; }
        .activity-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .dot-green { background: #10b981; } .dot-blue { background: #3b82f6; } .dot-gold { background: #f59e0b; }
    </style>
</head>
<body>
    <div class="header-title"><img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}"> WPMIS — WELLNESS PROGRAMME MANAGEMENT & IMPACT SYSTEM</div>
    <header class="nav-head"><nav class="nav-menu">
        <div class="pillgroup1"><span class="category-label">Dashboards</span>
            <a href="{{ route('dashboard.hr') }}" class="nav-link active"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
            <a href="{{ route('impact.analytics') }}" class="nav-link"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon"> Impact</a>
            <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon"> Retention</a>
            <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
        </div>
        <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
        <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
        <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
    </nav></header>

    <h1 style="padding: 0 4%; margin-bottom: 5px;">HR Overview Dashboard</h1>
    <p style="padding: 0 4%; margin-top: 0; color: #6b7280; font-size: 14px;">WPMIS — Wellness Programme Management & Impact System - Semester 1, 2026</p>

    <div class="split-row">LEADING INDICATORS — SYSTEM ADOPTION</div>
    <div class="kpi-grid-4">
        <div class="card"><div class="card-label">REGISTRATION RATE <span class="badge badge-leading">● Leading</span></div><div class="metric-value" style="color: #166534;">{{ $registrationRate }}%</div><div class="metric-sub">{{ $registeredStaff }} of {{ $totalStaff }} staff</div></div>
        <div class="card"><div class="card-label">CALENDAR INVITE ACCEPTANCE <span class="badge badge-leading">● Leading</span></div><div class="metric-value" style="color: #166534;">81%</div><div class="metric-sub">319 accepted invites</div></div>
        <div class="card"><div class="card-label">CONSENT COMPLETION <span class="badge badge-leading">● Leading</span></div><div class="metric-value" style="color: #166534;">88%</div><div class="metric-sub">before event day</div></div>
        <div class="card"><div class="card-label">POINTS EARNED <span class="badge badge-leading">● Leading</span></div><div class="metric-value" style="color: #166534;">{{ number_format($totalPointsEarned) }}</div><div class="metric-sub">this semester across all staff</div></div>
    </div>

    <div class="split-row">LAGGING INDICATORS — WELLNESS OUTCOMES</div>
    <div class="kpi-grid-2">
        <div class="card"><div class="card-label">WELLNESS IMPACT SCORE <span class="badge badge-lagging">● Lagging</span></div><div class="metric-value" style="color: #166534;">+{{ $avgLift }}</div><div class="metric-sub">avg WHO-5 pre – post delta</div></div>
        <div class="card"><div class="card-label">SUSTAINED PARTICIPATION <span class="badge badge-lagging">● Lagging</span></div><div class="metric-value" style="color: #02338d;">{{ $sustainedParticipationRate }}%</div><div class="metric-sub">attended 2+ events</div></div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; padding: 0 4% 20px;">
        <div class="card">
            <div class="card-label">Participation by wellness category</div>
            <div class="metric-sub" style="margin-bottom: 20px;">Registered vs attended across 4 SAMHSA dimensions</div>
            <div style="height: 250px; width: 100%;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-label">Recent activity</div>
            <div class="activity-list">
                @foreach($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-dot {{ $activity['marker'] === 'marker-green' ? 'dot-green' : ($activity['marker'] === 'marker-blue' ? 'dot-blue' : 'dot-gold') }}"></div>
                        <div>{!! $activity['text'] !!}<br><span style="color:#9ca3af; font-size:11px;">{{ $activity['time'] }}</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="split-row" style="margin-top: 10px;">EVENT INSIGHTS</div>
    <div class="kpi-grid-2" style="grid-template-columns: 1fr 2fr;">
        <div class="card">
            <div class="card-label">MOST ATTENDED EVENT</div>
            <div class="metric-value" style="font-size: 24px; line-height: 1.2;">{{ $mostAttendedEvent ? $mostAttendedEvent->event->name : 'No data yet' }}</div>
            <div class="metric-sub">{{ $mostAttendedEvent ? $mostAttendedEvent->total . ' staff registered' : '—' }}</div>
        </div>
        <div class="card">
            <div class="card-label" style="margin-bottom: 15px;">Qualitative Post-Event Feedback</div>
            <div style="display: flex; flex-direction: column; gap: 15px; max-height: 200px; overflow-y: auto; padding-right: 10px;">
                @forelse($qualitativeFeedback as $fb)
                    <div style="background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #02338d;">
                        <p style="font-size: 13px; color: #1f2937; font-style: italic;">"{{ $fb->comments }}"</p>
                        <p style="font-size: 11px; color: #6b7280; margin-top: 8px;">— {{ $fb->registration->user->name }} at {{ $fb->registration->event->name }}</p>
                    </div>
                @empty
                    <p style="font-size: 13px; color: #6b7280;">No written feedback submitted yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Physical', 'Mental', 'Financial', 'Social'],
                    datasets: [
                        {
                            label: 'Registered',
                            data: [{{ $physicalCount }}, {{ $mentalCount }}, {{ $financialCount }}, {{ $socialCount }}],
                            backgroundColor: '#e5e7eb',
                            borderRadius: 4
                        },
                        {
                            label: 'Attended',
                            data: [{{ $physicalCount * 0.8 }}, {{ $mentalCount * 0.8 }}, {{ $financialCount * 0.8 }}, {{ $socialCount * 0.8 }}], 
                            backgroundColor: '#02338d',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
</body>
</html>