<!DOCTYPE html>
<html lang="en">

    <head>
        <!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="This is the plain base page for IS project.">

        <title>Wellness Impact Dashboard</title>

        <!-- Meta tag for the impact analytics route -->
        <meta name="impact-route" content="{{ route('impact.analytics') }}">

        <link rel="icon" href="../assets/Strathmore_uni_logo-notext.png" type="image/x-icon">
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        @vite(['resources/css/hr_view_style.css', 'resources/js/app.js', 'resources/js/hr_impact_filter.js'])
    </head>

    <body>
        <!--data about the page that is seen-->
            <div class="header-title">
                <img src="../assets/Strathmore_uni_logo-notext.png" alt="Strathmore University Logo">
                Strathmore University - Wellness Programme Management & Impact System (WPMIS)
            </div>

        <header class="nav-head">
            <!--first category at the top of the page-->
            <!--navigation links-->
            <nav class="nav-menu">
                <div class="pillgroup1">
                    <span class="category-label">Dashboards</span> 

                    <a href="{{ route('dashboard.hr') }}" class="nav-link"> 
                        <img src="/images/analytics-icon.png" alt="analytics icon" class="nav-icon"> Overview
                    </a> 

                    <a href="{{ route('impact.analytics') }}" class="nav-link active">
                        <img src="/images/Impact-icon.png" alt="impact icon" class="nav-icon" id="impact-icon">Impact
                    </a> 

                    <a href="{{ route('retention.metrics') }}" class="nav-link">
                       <img src="/images/retention-icon1.png" alt="retention icon" class="nav-icon" id="retention-icon"> Retention
                    </a>

                    <a href="{{ route('department.analysis') }}" class="nav-link">
                        <img src="/images/department-icon.png" alt="department icon" class="nav-icon">Departmental
                    </a>
                </div>
                
                <!--second category at the top of the page-->
                <a href="{{ route('attendees.index') }}" class="nav-link">
                    <img src="/images/attendance-icon.png" alt="attendees icon" class="nav-icon">Attendees
                </a> 

                <a href="{{ route('events.management') }}" class="nav-link">
                    <img src="/images/events-icon.png" alt="events icon" class="nav-icon"> Events
                </a> 

                <a href="{{ route('settings.edit') }}" class="nav-link">
                    <img src="/images/settings-icon.png" alt="settings icon" class="nav-icon">Settings
                </a>
            </nav>
        </header>

        <!--Title-->
        <h1>Wellness Impact Score</h1>

            <p class="caption">WHO-5 Well-Being Index - validated instrument (score 0–25). Score below 13 indicates poor wellbeing. Pre vs post comparison per event.</p>

            <!-- MAIN CONTENT AREA-->
            <main class="dashboard-wrapper">
           
            <!-- Reusing dashboard flex row to map 4 cards across the display monitor -->
            <div class="dashboard-row cards-container-wrapper">
                
                <!-- Card 1: Avg WHO-5 Lift -->
                <div class="container metric-card">
                    <span class="card-label">AVG WHO-5 LIFT</span>
                    <div class="metric-value text-green">{{ $avgLift }}</div>
                    <div class="card-subtext">points gained (0–25 scale)</div>
                </div>

                <!-- Card 2: Avg Satisfaction -->
                <div class="container metric-card">
                    <span class="card-label">AVG SATISFACTION - PRE EVENT</span>
                    <div class="metric-value text-blue">{{ $avgPreScore }}/5</div>
                </div>

                <!-- Card 3: 4-6 Wk Follow-up -->
                <div class="container metric-card">
                    <span class="card-label">AVG SATISFACTION - POST EVENT</span>
                    <div class="metric-value text-blue">{{ $avgPostScore }}/5</div>
                </div>

                <!-- Card 4: Events in View how many distinct events 
                 have collected wellness evaluation submissions-->
                <div class="container metric-card">
                    <span class="card-label">EVENTS WITH WELLNESS RESPONSES</span>
                    <div class="metric-value text-dark">{{ $eventsInView }}</div>
                    <div class="card-subtext">of {{ $eventsThisSemester }} this Academic Year</div>
                </div>
                
            </div> <!-- Close cards-container-wrapper -->

            
            <!-- Flex wrap allows these button pills to cascade smoothly on mobile screen views -->
            <!--  Filter Pill Selection Track with JavaScript Hooks -->
            <div class="filter-pill-group">
                <!-- We use data-category attributes so JavaScript knows which section was clicked -->
                <button type="button" data-category="all" class="filter-pill {{ $activeFilter === 'all' ? 'active' : '' }}">All</button> 
                <button type="button" data-category="physical" class="filter-pill {{ $activeFilter === 'physical' ? 'active' : '' }}">Physical</button> 
                <button type="button" data-category="mental" class="filter-pill {{ $activeFilter === 'mental' ? 'active' : '' }}">Mental</button>
                <button type="button" data-category="financial" class="filter-pill {{ $activeFilter === 'financial' ? 'active' : '' }}">Financial</button>
                <button type="button" data-category="social" class="filter-pill {{ $activeFilter === 'social' ? 'active' : '' }}">Social</button>
            </div>
         
            
                 <div class="dashboard-row split-row">

                <!-- Left Graphic Block: Integrated WHO-5 Dynamic Chart Box Container -->
                    <div class="container chart-display-card">
                        <h2>WHO-5 Pre / Post</h2>
                        <p class="chart-subtitle-text">Average group scores on 0-25 scale</p>
                        
                        <!-- Outer custom chart alignment and scaling layer -->
                        <div class="impact-chart-wrapper">
                            
                            <!-- Y-Axis Vertical Scale Numbers (Proportional to the 0-25 WHO-5 scale) -->
                            <div class="impact-yaxis-labels">
                                <span>25</span>
                                <span>14</span>
                                <span>7</span>
                                <span>0</span>
                            </div>

                           
                        

                <!-- The Central Graph View Window Track -->
                    <div class="impact-viewport-track">
                        
                        <!-- Custom WHO-5 Guideline Background Track Layer -->
                        <div class="impact-grid-lines">
                            <div class="impact-line level-25" style="bottom: 100%;"></div>
                            <div class="impact-line level-14" style="bottom: 56%;"></div>
                            <div class="impact-line level-7" style="bottom: 28%;"></div>
                            <div class="impact-line-base" style="bottom: 0;"></div>
                        </div>

            <!--  table bars-->
                        <!-- STEP 4: AUTOMATED DYNAMIC 12-MONTH STRIP ROW LOOP -->
                <div class="impact-bars-strip" id="graph-bars-container">
                    @foreach($graphBars as $monthName => $bars)
                        
                        <!-- Individual Month Column Slot Block Node -->
                        <div class="impact-month-column">
                            <div class="impact-pair-group">
                                
                                <!-- Pre-Event Pillar Pulls calculated percentage height -->
                                <div class="impact-pillar bar-pre" style="--bar-height: {{ $bars['pre_height'] }}%;"></div>
                                
                                <!-- Post-Event Pillar Pulls calculated percentage height -->
                                <div class="impact-pillar bar-post" style="--bar-height: {{ $bars['post_height'] }}%;"></div>
                            
                            </div>
                            <!-- Month Text Label printed right underneath the baseline floor line -->
                            <span class="impact-xaxis-label">{{ $monthName }}</span>

                            <!-- FLOATING INFORMATION CARD INTERACTIVE OVERLAY BOX TOOLTIP -->
                            <div class="impact-hover-tooltip">
                                <h4 class="impact-tooltip-title">{{ $monthName }}</h4>
                                <p class="impact-tooltip-row text-light">Pre-event: <span>{{ $bars['pre_raw'] }}</span></p>
                                <p class="impact-tooltip-row text-blue">Post-event: <span>{{ $bars['post_raw'] }}</span></p>
                            </div>
                        </div>

                    @endforeach
                </div> <!-- End of #graph-bars-container -->
        </div> <!-- End of .impact-viewport-track -->
    </div> <!-- End of .impact-chart-wrapper -->

            
            </div>
        </div>

            <!-- DYNAMIC  DATA TABLE BODY ROW -->
            <div class="table-card-wrapper">
                <div class="table-responsive-container">
                    <table class="impact-data-table">
                        <thead>
                            <tr>
                                <th scope="col">Event</th>
                                <th scope="col" class="center-text">Number Registered</th>
                                <th scope="col" class="center-text">Pre</th>
                                <th scope="col" class="center-text">Post</th>
                                <th scope="col" class="center-text">Improvement Score</th>
                                <th scope="col" class="center-text">Follow-up</th>
                            </tr>
                        </thead>

                                             
                        <tbody id="table-rows-container">
                            <!-- The loop reads packaged collection variables from the controller -->
                            @foreach($eventsTableRows as $row)
                                <tr>
                                    <!-- 1. Event Name Column -->
                                    <td class="event-name-cell">{{ $row['name'] }}</td>
                                    
                                    <!-- 2. Number of Participants (n) Column -->
                                    <td class="center-text metric-number">{{ $row['participants'] }}</td>
                                    
                                    <!-- 3. Average Pre-Event Score Column -->
                                    <td class="center-text metric-number">{{ $row['pre_avg'] }}</td>
                                    
                                    <!-- 4. Average Post-Event Score Column -->
                                    <td class="center-text metric-number">{{ $row['post_avg'] }}</td>
                                    
                                    <!-- 5. Growth Lift Column (We apply conditional text colors dynamically) -->
                                    <!-- $row['is_positive'] checks if the lift value is positive to flag it green -->
                                    <td class="center-text font-bold metric-number text">
                                        {{ $row['lift'] }}
                                    </td>
                                    
                                    <!-- 6. Follow-up Baseline Metrics Column -->
                                    <td class="center-text metric-number">{{ $row['follow_up'] }}</td>
                                </tr>
                            @endforeach

                            <!-- SAFE FALLBACK SHIELD: If your database table has 0 total rows, print this notice -->
                            @if(count($eventsTableRows) === 0)
                                <tr>
                                    <td colspan="6" class="center-text dash-placeholder" style="padding: 30px;">
                                        No active event evaluation logs found for this filter category.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </body>
</html>