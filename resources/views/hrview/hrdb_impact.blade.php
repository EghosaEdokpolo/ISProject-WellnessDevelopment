<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Wellness Impact Score</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
        <!-- Load Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

        <!-- Load Chart.js via global CDN instantly -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        
        @vite(['resources/css/hr_view_style.css', 'resources/js/app.js', 'resources/css/HR_impact.css', 'resources/js/hr_impact_filter.js'])
    </head>
    
    <body>
        <div class="header-title"><img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}"> WPMIS — WELLNESS PROGRAMME MANAGEMENT & IMPACT SYSTEM</div>
        <header class="nav-head"><nav class="nav-menu">
            <div class="pillgroup1"><span class="category-label">Dashboards</span>
                <a href="{{ route('dashboard.hr') }}" class="nav-link"><img src="{{ asset('images/analytics-icon.png') }}" class="nav-icon"> Overview</a>
                <a href="{{ route('impact.analytics') }}" class="nav-link active"><img src="{{ asset('images/Impact-icon.png') }}" class="nav-icon"> Impact</a>
                <a href="{{ route('retention.metrics') }}" class="nav-link"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon"> Retention</a>
                <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
            </div>
            <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
            <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
            <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
        </nav></header>

        <div style="padding: 0 4%; margin-bottom: 20px;">
            <h1 style="margin-bottom: 5px;">Wellness Impact Score <span style="font-size:12px; background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:12px; vertical-align:middle;">● Lagging</span></h1>
            <p style="margin-top:0; color:#6b7280; font-size:14px;">WHO-5 Well-Being Index — validated instrument (score 0–25). Pre vs post comparison per event.</p>
        </div>

        <!-- Filter Navigation Links -->
        <div class="filter-row" style="display: flex; flex-wrap: wrap; gap: 10px; padding: 10px 4%;">
            <button data-category="all" class="pill filter-pill-btn active" style="cursor: pointer;">All</button>
            <button data-category="physical" class="pill filter-pill-btn" style="cursor: pointer;">Physical</button>
            <button data-category="mental" class="pill filter-pill-btn" style="cursor: pointer;">Mental</button>
            <button data-category="financial" class="pill filter-pill-btn" style="cursor: pointer;">Financial</button>
            <button data-category="social" class="pill filter-pill-btn" style="cursor: pointer;">Social</button>
            <button data-category="intellectual" class="pill filter-pill-btn" style="cursor: pointer;">Intellectual</button>
            <button data-category="occupational" class="pill filter-pill-btn" style="cursor: pointer;">Occupational</button>
            <button data-category="environmental" class="pill filter-pill-btn" style="cursor: pointer;">Environmental</button>
            <button data-category="spiritual" class="pill filter-pill-btn" style="cursor: pointer;">Spiritual</button>
        </div>

        <!-- High-Impact banner container -->
        <div id="highest-lowest-banner-row" style="padding: 0 4% 20px; display: block;">
            <div class="card" style="background: #f0fdf4; border-color: #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 0; font-weight: 700; color: #166534; font-size: 14px;">Highest Impact Dimension</p>
                    <p id="highest-dim-text" style="margin: 0; font-size: 20px; font-weight: 800; color: #166534; text-transform: capitalize;">{{ $highestDim }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: 700; color: #991b1b; font-size: 14px;">Lowest Impact Dimension</p>
                    <p id="lowest-dim-text" style="margin: 0; font-size: 20px; font-weight: 800; color: #991b1b; text-transform: capitalize;">{{ $lowestDim }}</p>
                </div>
            </div>
        </div>

        <!-- Metrics KPI Row Container Layout -->
        <div id="impact-kpi-cards-wrapper" class="kpi-grid-4">
            <div class="card"><div class="card-label">AVG WHO-5 LIFT</div><div class="metric-value" style="color:#0a7b2b;">{{ $avgLiftGlobal > 0 ? '+' : '' }}{{ round($avgLiftGlobal ?? 0, 1) }}</div><div class="metric-sub">points gained</div></div>
            <div class="card"><div class="card-label">AVG PRE-EVENT SCORE</div><div class="metric-value">{{ round($avgPreGlobal, 1) }}</div><div class="metric-sub">out of 25</div></div>
            <div class="card"><div class="card-label">AVG POST-EVENT SCORE</div><div class="metric-value">{{ round($avgPostGlobal, 1) }}</div><div class="metric-sub">out of 25</div></div>
            <div class="card"><div class="card-label">EVENTS IN VIEW</div><div class="metric-value">{{ count($eventTable) }}</div><div class="metric-sub">this semester</div></div>
        </div>

        <!-- Dynamic Graph Canvas Engine Mapping -->
        <div class="card" style="margin: 0 4% 20px;">
            <div class="card-label">WHO-5 Pre / Post Progress Breakdown</div>
            <div style="height: 250px; width: 100%; position: relative; margin-top: 15px;">
                <canvas id="impactCanvasEngine" 
                        data-categories="{{ json_encode(array_keys($categories)) }}"
                        data-pre-avgs="{{ json_encode(array_column($categories, 'pre_avg')) }}"
                        data-post-avgs="{{ json_encode(array_column($categories, 'post_avg')) }}">
                </canvas>
            </div>
        </div>

        <div style="padding: 0 4% 40px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Event</th>
                        <th>Category</th>
                        <th>Pre Avg</th>
                        <th>Post Avg</th>
                        <th>Lift (Improvement)</th>
                        <th>Survey Status</th>
                    </tr>
                </thead>
                <tbody id="table-rows-container">
                    @foreach($eventTable as $row)
                        <tr>
                            <td style="text-align: left;"><strong>{{ $row['name'] }}</strong></td>
                            <td>{{ $row['category'] }}</td>
                            <td>{{ $row['pre_avg'] }}</td>
                            <td>{{ $row['post_avg'] }}</td>
                            <td class="{{ (float)$row['lift'] > 0 ? 'lift-positive' : ((float)$row['lift'] < 0 ? 'lift-negative' : '') }}" style="font-weight: 700;">
                                {{ $row['lift'] }}
                            </td>
                            <td style="font-weight: 700; color: {{ $row['pre_avg'] !== '—' && $row['post_avg'] !== '—' ? '#0a7b2b' : '#991b1b' }};">
                                {{ $row['survey_status'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
