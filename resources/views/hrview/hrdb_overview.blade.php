<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>HR Overview Dashboard</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/hr_view_style.css', 'resources/css/HR_overview.css', 'resources/js/app.js', 'resources/js/HR_overview.js'])
        
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

    <!--The Indicator Framework Card -->
    <div class="container indicator-legend-box">
        <h3>INDICATOR FRAMEWORK</h3>

        <div class="legend-row">
            <span class="legend-dot dot-leading"></span>
            <p><strong>Leading</strong> - early signal of system adoption</p>
        </div>

        <div class="legend-row">
            <span class="legend-dot dot-lagging"></span>
            <p><strong>Lagging</strong> - real-world wellness outcome</p>
        </div>
    </div>

    <!-- LEADING INDICATORS SECTION -->
    <div class="split-row">LEADING INDICATORS — SYSTEM ADOPTION</div>
        <div class="kpi-grid-4">
            <div class="card">
                <div class="card-label">REGISTRATION RATE <span class="badge badge-leading">● Leading</span></div>
            <div class="metric-value" style="color: #166534;">{{ $registrationRate }}%</div>
        <div class="metric-sub">{{ $registeredStaff }} of {{ $totalStaff }} staff</div></div>
        
        <!-- Card 2: Calendar Invite Acceptance / Survey Velocity -->
        <div class="card">
            <div class="card-label">SURVEY VELOCITY <span class="badge badge-leading">● Leading</span></div>
                <div class="metric-value" style="color: #166534;">{{ $surveyVelocityRate }}%</div>
            <div class="metric-sub">Submitted within 24h post event (past 30 days)</div>
        </div>

        <!-- Card 3: Consent Completion Rate -->
        <div class="card">
            <div class="card-label">CONSENT COMPLETION <span class="badge badge-leading">● Leading</span></div>
                <div class="metric-value" style="color: #166534;">{{ $consentCompletionRate }}%</div>
            <div class="metric-sub">Waivers signed for events next 30 days</div>
        </div>

        <div class="card">
            <div class="card-label">POINTS EARNED <span class="badge badge-leading">● Leading</span></div>
                <div class="metric-value" style="color: #166534;">{{ number_format($totalPointsEarned) }}</div>
            <div class="metric-sub">this academic year across all staff</div>
        </div>
    </div>

    <!-- LAGGING INDICATORS SECTION -->
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
                
                <canvas id="categoryChart" 
                        data-reg-physical="{{ $regPhysical }}" data-att-physical="{{ $attPhysical }}"
                        data-reg-mental="{{ $regMental }}" data-att-mental="{{ $attMental }}"
                        data-reg-financial="{{ $regFinancial }}" data-att-financial="{{ $attFinancial }}"
                        data-reg-social="{{ $regSocial }}" data-att-social="{{ $attSocial }}"
                        data-reg-intellectual="{{ $regIntellectual }}" data-att-intellectual="{{ $attIntellectual }}"
                        data-reg-occupational="{{ $regOccupational }}" data-att-occupational="{{ $attOccupational }}"
                        data-reg-environmental="{{ $regEnvironmental }}" data-att-environmental="{{ $attEnvironmental }}"
                        data-reg-spiritual="{{ $regSpiritual }}" data-att-spiritual="{{ $attSpiritual }}">
                </canvas>


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
            <div class="card-label" style="margin-bottom: 15px;">Qualitative Post-Event Feedback 
                <a href="{{ route('hr.feedback.logs') }}" style="color: #02338d; text-transform: none; font-weight: 700; text-decoration: none;">View More →</a>
            
            </div>
            
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

    
    </body>
</html>