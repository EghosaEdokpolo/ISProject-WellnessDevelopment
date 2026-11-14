<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sustained Participation Rate</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

        <!-- Pull in Chart.js via CDN for the loyalty distribution chart -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        @vite(['resources/css/hr_view_style.css', 'resources/css/HR_retention.css', 'resources/js/app.js', 'resources/js/hr_retention_filter.js'])
        
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
                    <a href="{{ route('retention.metrics') }}" class="nav-link active"><img src="{{ asset('images/retention-icon1.png') }}" class="nav-icon" id="retention-icon"> Retention</a>
                    <a href="{{ route('department.analysis') }}" class="nav-link"><img src="{{ asset('images/department-icon.png') }}" class="nav-icon"> Departmental</a>
                </div>
                <a href="{{ route('attendees.index') }}" class="nav-link"><img src="{{ asset('images/attendance-icon.png') }}" class="nav-icon"> Attendees</a>
                <a href="{{ route('events.management') }}" class="nav-link"><img src="{{ asset('images/events-icon.png') }}" class="nav-icon"> Events</a>
                <a href="{{ route('settings.edit') }}" class="nav-link"><img src="{{ asset('images/settings-icon.png') }}" class="nav-icon"> Settings</a>
            </nav>
        </header>

        <div style="padding: 0 4%; margin-bottom: 20px;">
            <h1>Sustained Participation Rate <span style="font-size:12px; background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:12px; vertical-align:middle;">● Lagging</span></h1>
            <p style="margin-top:0; color:#6b7280; font-size:14px;">% of staff attending more than one wellness event per academic year.</p>
        </div>

        <!-- KEPT YOUR ORIGINAL TOP CARD LAYOUT EXACTLY AS IS -->
        <div class="big-kpi">
            <div class="number">{{ $retention['sustained_participation'] }}%</div>
            <div class="label">Sustained Participation Rate</div>
            <div class="sub">{{ $retention['period']['start'] }} → {{ $retention['period']['end'] }}</div>
        </div>

        <div class="sub-kpis">
            <div class="sub-kpi">
                <div class="lab">Active Staff</div>
                <div class="num">{{ $retention['total_active_staff'] }}</div>
            </div>
            <div class="sub-kpi">
                <div class="lab">Sustained Attendees</div>
                <div class="num">{{ $retention['sustained_attendees'] }}</div>
            </div>
        </div>

                <!-- MIDDLE ROW CONFIGURATION -->
        <div class="charts-grid-row">
            
            <!-- 1. Left Card: The Departmental Drop-Off  Matrix) -->
            <div class="card" style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); height: 350px; display: flex; flex-direction: column;">
                <div class="card-label" style="margin-bottom: 15px; font-weight: bold; color: #4b5563; font-size: 15px;">📊 Departmental Drop-Off Heatmap Matrix</div>
                <span style="margin-bottom: 7px; font-size: 13px;">100-60% - 🟢 . 59-30% - 🟡 . 29-0% - 🔴</span>
                
                <div style="flex: 1; overflow-y: auto; padding-right: 4px;">
                    <table class="heatmap-table">
                        <thead style="position: sticky;">
                            <tr>
                                <th style="position: sticky; top: 0; background: #ffffff !important; z-index: 10; text-align: left;">Department</th>
                                <th style="position: sticky; top: 0; background: #ffffff !important; z-index: 10;">Q1 Start</th>
                                <th style="position: sticky; top: 0; background: #ffffff !important; z-index: 10;">Q2 Peak</th>
                                <th style="position: sticky; top: 0; background: #ffffff !important; z-index: 10;">Q3 Term</th>
                                <th style="position: sticky; top: 0; background: #ffffff !important; z-index: 10;">Retention Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retention['heatmap_data'] as $row)
                                <tr>
                                    <!-- Department Column -->
                                    <td><strong>{{ $row['department'] }}</strong></td>
                                    
                                    <!-- Q1 Column  -->
                                    <td class="{{ $row['q1'] >= 60 ? 'heat-high' : ($row['q1'] >= 30 ? 'heat-medium' : 'heat-low') }}">
                                        {{ $row['q1'] }}%
                                    </td>
                                    
                                    <!-- Q2 Column: Dynamic Color Mapping -->
                                    <td class="{{ $row['q2'] >= 60 ? 'heat-high' : ($row['q2'] >= 30 ? 'heat-medium' : 'heat-low') }}">
                                        {{ $row['q2'] }}%
                                    </td>
                                    
                                    <!-- Q3 Column: Dynamic Color Mapping -->
                                    <td class="{{ $row['q3'] >= 60 ? 'heat-high' : ($row['q3'] >= 30 ? 'heat-medium' : 'heat-low') }}">
                                        {{ $row['q3'] }}%
                                    </td>
                                    
                                    <!-- Status Pill Column -->
                                    <td>
                                        <span class="status-pill {{ $row['q3'] >= 50 ? 'status-stable' : 'status-drop' }}">
                                            {{ $row['q3'] >= 50 ? 'Stable' : 'Critical Drop' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

            <!-- 2. Right Card: Attendance Loyalty Frequency Distribution (SET TO MATCHING 350PX HEIGHT) -->
            <div class="card" style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); height: 350px;">
                <div class="card-label" style="margin-bottom: 15px; font-weight: bold; color: #4b5563; font-size: 15px;">⏱️ Attendance Loyalty Frequency Distribution</div>
                
                <div style="height: 270px; position: relative; width: 100%;">
                    <canvas id="loyaltyDistributionChart"
                            data-labels="{{ json_encode(array_keys($retention['loyalty_frequency'])) }}"
                            data-values="{{ json_encode(array_values($retention['loyalty_frequency'])) }}">
                    </canvas>
                </div>
            </div>

        </div>


        <!-- GRANULAR INDIVIDUAL LOYALTY SCORES DATA TABLE PLACED INSIDE A CARD WITH ROUNDED CORNERS -->
        <div style="padding: 0 4% 40px;">
            <div class="card" style="padding: 20px 0 0; border-radius: 12px; overflow: hidden; background: white;">
                <div class="card-label" style="padding-left: 20px; margin-bottom: 15px; font-size: 20px;">👤 Individual Staff Loyalty Scores</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding-left: 20px;">Staff Member Name</th>
                            <th style="text-align: left;">Assigned Department</th>
                            <th>Events Attended</th>
                            <th>Loyalty Metrics Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retention['individual_scores'] as $staff)
                            <tr>
                                <td style="text-align: left; padding-left: 20px;"><strong>{{ $staff['name'] }}</strong></td>
                                <td style="text-align: left; color:#4b5563;">{{ $staff['department'] }}</td>
                                <!-- Displays dynamically calculated totals count dynamically -->
                                <td><strong>{{ $staff['attended_count'] }} / {{ $retention['total_events_count'] }}</strong></td>
                                <td>
                                    <!-- Cleaned up linear bar components entirely as requested -->
                                    <span class="loyalty-badge" style="
                                        background: {{ $staff['loyalty_rate'] >= 75 ? '#dcfce7' : ($staff['loyalty_rate'] >= 40 ? '#fef3c7' : '#fee2e2') }};
                                        color: {{ $staff['loyalty_rate'] >= 75 ? '#166534' : ($staff['loyalty_rate'] >= 40 ? '#92400e' : '#991b1b') }};
                                        font-weight: bold; font-size: 13px;
                                    ">
                                        {{ $staff['loyalty_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </body>
</html>
