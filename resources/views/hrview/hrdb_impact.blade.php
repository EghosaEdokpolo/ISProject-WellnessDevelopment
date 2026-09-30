<!DOCTYPE html>
<html lang="en">

    <head>
        <!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="This is the plain base page for IS project.">

        <title>First webpage screen</title>
        <link rel="icon" href="../assets/Strathmore_uni_logo-notext.png" type="image/x-icon">
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
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

        <!--page details-->
        <p> Based on the"WHO-5 Well-Being Index". Score below 13 indicates poor wellbeing for Pre vs post comparison per event.</p>

                <!-- ========================================================
             MAIN CONTENT WINDOW AREA
             ======================================================== -->
        <main class="dashboard-wrapper">

            <!-- MASTER DASHBOARD TITLE AND DETAILS BLOCK -->
            <div class="dashboard-title-area">
                <h1>Wellness Impact Score</h1>
                <!-- Mockup Status Label Badge -->
                <span class="badge badge-lagging">
                    <span class="badge-dot"></span> Lagging
                </span>
                <p class="caption">WHO-5 Well-Being Index — validated instrument (score 0–25). Score below 13 indicates poor wellbeing. Pre vs post comparison per event.</p>
            </div>

            <!-- ========================================================
                 PHASE 1: CATEGORY FILTER BUTTON PILLS
                 ======================================================== -->
            <!-- Flex wrap allows these button pills to cascade smoothly on mobile screen views -->
            <div class="filter-pill-group">
                <a href="#" class="filter-pill active">All</a> 
                <a href="#" class="filter-pill">Physical</a> 
                <a href="#" class="filter-pill">Mental</a>
                <a href="#" class="filter-pill">Financial</a>
                <a href="#" class="filter-pill">Social</a>
            </div>

            <!-- ========================================================
                 PHASE 2: TOP SUMMARY HIGHLIGHT CARD ROW
                 ======================================================== -->
            <!-- Reusing your favorite utility dashboard flex row to map 4 cards across the display monitor -->
            <div class="dashboard-row">
                
                <!-- Card 1: Avg WHO-5 Lift -->
                <div class="container metric-card">
                    <span class="card-label">AVG WHO-5 LIFT</span>
                    <div class="metric-value text-green">+6.4</div>
                    <div class="card-subtext">points gained (0–25 scale)</div>
                </div>

                <!-- Card 2: Avg Satisfaction -->
                <div class="container metric-card">
                    <span class="card-label">AVG SATISFACTION</span>
                    <div class="metric-value text-blue">4.3/5</div>
                    <div class="card-subtext">across filtered events</div>
                </div>

                <!-- Card 3: 4-6 Wk Follow-up -->
                <div class="container metric-card">
                    <span class="card-label">4–6 WK FOLLOW-UP</span>
                    <div class="metric-value text-orange">17.2</div>
                    <div class="card-subtext">sustained wellbeing</div>
                </div>

                <!-- Card 4: Events in View -->
                <div class="container metric-card">
                    <span class="card-label">EVENTS IN VIEW</span>
                    <div class="metric-value text-dark">6</div>
                    <div class="card-subtext">of 6 this semester</div>
                </div>

            </div>

            <!-- ========================================================
                 PHASE 3: GRAPHICS ANALYTICS ROW (BARS & TREND LINE FRAME)
                 ======================================================== -->
            <div class="dashboard-row split-row">

                <!-- Left Graphic Block: Triple Column Bar Chart Box Container -->
                <div class="container chart-display-card">
                    <h2>WHO-5 Pre / Post / Follow-up by Event</h2>
                    <p class="chart-subtitle-text">Average group scores on 0-25 scale · grey = follow-up not yet collected</p>
                    
                    <!-- GRAPH CANVAS PLACEHOLDER: We turn this card into a vertical box column -->
                    <div class="mock-graphic-space">
                        <p class="mock-placeholder-text">[ TRIPLE BAR GRAPH CHART CANVAS PLACEHOLDER ]</p>
                    </div>
                </div>

                <!-- Right Graphic Block: Impact Lift Trend Line Box Container -->
                <div class="container chart-display-card">
                    <h2>Impact lift trend</h2>
                    <p class="chart-subtitle-text">WHO-5 gain (post - pre) per event</p>
                    
                    <!-- GRAPH CANVAS PLACEHOLDER -->
                    <div class="mock-graphic-space">
                        <p class="mock-placeholder-text">[ TREND LINE GRAPH CHART CANVAS PLACEHOLDER ]</p>
                    </div>
                </div>

            </div>

            <!-- ========================================================
                 PHASE 4: DENSE REGISTRATION METRICS DATA TABLE
                 ======================================================== -->
            <div class="table-card-wrapper">
                <div class="table-responsive-container">
                    <table class="impact-data-table">
                        <thead>
                            <tr>
                                <th scope="col">Event</th>
                                <th scope="col" class="center-text">n</th>
                                <th scope="col" class="center-text">Pre</th>
                                <th scope="col" class="center-text">Post</th>
                                <th scope="col" class="center-text">Lift</th>
                                <th scope="col" class="center-text">Follow-up</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="event-name-cell">Yoga & Mindfulness</td>
                                <td class="center-text metric-number">34</td>
                                <td class="center-text metric-number">13.1</td>
                                <td class="center-text metric-number">18.5</td>
                                <td class="center-text text-green font-bold">+5.4</td>
                                <td class="center-text metric-number">17.0</td>
                            </tr>
                            <tr>
                                <td class="event-name-cell">Staff Hike — Karura Forest</td>
                                <td class="center-text metric-number">52</td>
                                <td class="center-text metric-number">12.0</td>
                                <td class="center-text metric-number">19.7</td>
                                <td class="center-text text-green font-bold">+7.7</td>
                                <td class="center-text metric-number">18.0</td>
                            </tr>
                            <tr>
                                <td class="event-name-cell">Financial Wellness Forum</td>
                                <td class="center-text metric-number">41</td>
                                <td class="center-text metric-number">13.8</td>
                                <td class="center-text metric-number">17.7</td>
                                <td class="center-text text-green font-bold">+3.9</td>
                                <td class="center-text metric-number">16.3</td>
                            </tr>
                            <tr>
                                <td class="event-name-cell">Breathwork Session</td>
                                <td class="center-text metric-number">38</td>
                                <td class="center-text metric-number">12.2</td>
                                <td class="center-text metric-number">19.1</td>
                                <td class="center-text text-green font-bold">+6.9</td>
                                <td class="center-text metric-number">17.5</td>
                            </tr>
                            <tr>
                                <td class="event-name-cell">Cross-Dept Social Mixer</td>
                                <td class="center-text metric-number">96</td>
                                <td class="center-text metric-number">14.5</td>
                                <td class="center-text metric-number">20.8</td>
                                <td class="center-text text-green font-bold">+6.3</td>
                                <td class="center-text dash-placeholder">—</td>
                            </tr>
                            <tr>
                                <td class="event-name-cell">Mental Health Talk</td>
                                <td class="center-text metric-number">64</td>
                                <td class="center-text metric-number">11.5</td>
                                <td class="center-text metric-number">19.5</td>
                                <td class="center-text text-green font-bold">+8.0</td>
                                <td class="center-text dash-placeholder">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Bottom Description Notice Footnote -->
                <p class="table-footnote-notice">WHO-5 Well-Being Index — validated instrument. Score 0–25. Score below 13 indicates poor wellbeing.</p>
            </div>

        </main>

    </body>
</html>