<!DOCTYPE html>
<html lang="en">

<head>
<!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="The the Hr / people and culture analytical dashboard used to view metrics relation to staff wellness in strathmore University.">

        <title>Dashboard-HR</title>

        <!--favicon-->
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

                    <a href="{{ route('impact.analytics') }}" class="nav-link">
                        <img src="/images/Impact-icon.png" alt="impact icon" class="nav-icon" id="impact-icon">Impact
                    </a> 

                    <a href="{{ route('retention.metrics') }}" class="nav-link active">
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
        <h1>Sustained RetentionRate</h1>

        <!--page details-->
            <p class="caption"> % of staff who attend more than one wellness event per academic year.</p>

        <main>

        <!--FOUR-COLUMN TOP HORIZONTAL METRICS ROW SECTION -->
        <div class="dashboard-row retention-top-cards">
            
            <!-- Summary Module Box 1: Sustained Rate -->
            <div class="container metric-card">
                <div class="card-header-line">
                    <span class="card-label">SUSTAINED RATE</span>
                </div>
                <div class="metric-value text-green">58%</div>
                <div class="card-subtext">attended 2+ events</div>
            </div>

            <!-- Summary Module Box 2: Repeat Participants -->
            <div class="container metric-card">
                <div class="card-header-line">
                    <span class="card-label">REPEAT PARTICIPANTS</span>
                </div>
                <!-- Balanced deep layout typography tracking colors -->
                <div class="metric-value text-purple">7</div>
                <div class="card-subtext">of 12 tracked</div>
            </div>

            <!-- Summary Module Box 3: Avg Loyalty Score -->
            <div class="container metric-card">
                <div class="card-header-line">
                    <span class="card-label">AVG LOYALTY SCORE</span>
                </div>
                <div class="metric-value text-orange">2.3/6</div>
                <div class="card-subtext">events per person avg</div>
            </div>

            <!-- Summary Module Box 4: Fully Loyal Count -->
            <div class="container metric-card">
                <div class="card-header-line">
                    <span class="card-label">FULLY LOYAL</span>
                </div>
                <div class="metric-value text-dark">1</div>
                <div class="card-subtext">attended every event</div>
            </div>

        </div>

        <!-- 5. SPLIT GRAPH ANALYSIS DUAL SECTION ROW -->
        <!-- Splits screen workspace side-by-side horizontally across large monitor view screens -->
        <div class="dashboard-row retention-split-row">
            
            <!-- Drop-off Analysis Chart Structure Frame Window -->
            <div class="container retention-chart-card">
                <h2>Drop-off analysis</h2>
                <p class="chart-subtitle-text">Registered vs attended per event — identifies drop-off points</p>
                
                <!-- Outer workspace alignment structural wrap layer -->
                <div class="dropoff-chart-wrapper">
                    
                    <!-- Y-Axis Vertical Metric Number Scale Stack Elements -->
                    <div class="chart-yaxis-labels">
                        <span>120</span>
                        <span>90</span>
                        <span>60</span>
                        <span>30</span>
                        <span>0</span>
                    </div>

                            <!-- The Central Graphic Viewing Window Frame Track Layer -->
                            <div class="chart-viewport-track">
                                
                                <!-- 3A. Background Guideline Lines (Stacked behind our bars) -->
                                <div class="chart-grid-lines">
                                    <div class="grid-line"></div> <!-- Represents the 120 level line -->
                                    <div class="grid-line"></div> <!-- Represents the 90 level line -->
                                    <div class="grid-line"></div> <!-- Represents the 60 level line -->
                                    <div class="grid-line"></div> <!-- Represents the 30 level line -->
                                    <div class="grid-line-base"></div> <!-- Represents the 0 solid ground line -->
                                </div>

                                <!-- 4A. MAIN ROW BAR STRIP CONTROLLER LAYER (New Step 4 Content) -->
                                <div class="chart-bars-strip">

                                    <!-- MONTH NODE 1: FEBRUARY -->
                                    <div class="chart-month-column">
                                        <!-- The pair container holds our two bars tightly side-by-side -->
                                        <div class="bars-pair-group">
                                            <!-- Custom properties map the heights dynamically out of 100% -->
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 33%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 28%;"></div>
                                        </div>
                                        <!-- Month label sitting underneath the baseline grid floor -->
                                        <span class="chart-xaxis-label">Feb</span>
                                    </div>

                                    <!-- MONTH NODE 2: MARCH -->
                                    <!-- We add the "active-column" utility class to style the card preview initially -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 53%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 43%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Mar</span>

                                        <!-- 5A. FLOATING OVERLAY INFORMATION TOOLTIP (New content block) -->
                                        <div class="chart-hover-tooltip">
                                            <h4 class="tooltip-title">Mar</h4>
                                            <p class="tooltip-row text-light">Registered: <span class="font-bold">60</span></p>
                                            <p class="tooltip-row text-dark">Attended: <span class="font-bold">52</span></p>
                                        </div>
                                    </div>

                                    <!-- MONTH NODE 3: APRIL -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 38%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 20%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Apr</span>
                                    </div>

                                    <!-- MONTH NODE 4: MAY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 37%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 32%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">May</span>
                                    </div>

                                    <!-- MONTH NODE 5: JUNE -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 92%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 80%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jun</span>
                                    </div>

                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>
                                    <!-- MONTH NODE 6: JULY -->
                                    <div class="chart-month-column">
                                        <div class="bars-pair-group">
                                            <div class="vertical-pillar dynamic-bar-reg" style="--bar-height: 62%;"></div>
                                            <div class="vertical-pillar dynamic-bar-att" style="--bar-height: 53%;"></div>
                                        </div>
                                        <span class="chart-xaxis-label">Jul</span>
                                    </div>

                            </div> <!-- End of .chart-viewport-track -->

                        </div> <!-- End of .dropoff-chart-wrapper -->
                </div>
            </div> <!-- End of .main-content-wrapper -->
        </div> <!-- End of .main-content-wrapper -->

        
           <!-- individual loyalty scores table -->

        <div class="table-card-wrapper">
            <h3>Individual loyalty scores</h3>
            <p class="caption-custom">Staff who attended at least one event·</p>

                <!-- Filter Form Component Element Group -->
                    <div class="filter-box-group">
                        <label for="departmentcategory" class="form_labels">Filter By</label>
                        
                        <!-- Input text field referencing your choices datalist tracking layout -->
                        <input type="text" name="departmentcategory" id="departmentcategory" list="departmentlist" placeholder="Select or type department...">    
                        
                        <datalist id="departmentlist">
                            <option value="SBS">
                            <option value="SCES">
                            <option value="STH">
                        </datalist>
                    </div>

        <div class="retention-table-row">
            <!-- Locked sticky static container frame panel wrapping the table matrix block wrapper -->
            <div class="table-container2">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left;">Name</th>
                            <th style="text-align: left;">Department</th>
                            <th style="text-align: center; width: 140px;">Attended</th>
                            <th style="text-align: left; width: 180px;">Loyalty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row Data Component element trace block item instance 1 -->
                        <tr>
                            <td class="name-column" style="text-align: left;">Fatuma Ali</td>
                            <td class="department-column" style="text-align: left;">Strathmore Business School</td>
                            <td class="column-text text-center">6/6</td>
                            <td>
                                <!-- Embedded dynamic miniature visual metric display progress bars components -->
                                <div class="retention-progress-wrapper">
                                    <span class="retention-progress-label font-bold text-green">100%</span>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row Data Component element trace block item instance 2 -->
                        <tr>
                            <td class="name-column" style="text-align: left;">Achieng Otieno</td>
                            <td class="department-column" style="text-align: left;">School of Computing & Engineering Sciences</td>
                            <td class="column-text text-center">5/6</td>
                            <td>
                                <!-- Embedded dynamic miniature visual metric display progress bars components -->
                                <div class="retention-progress-wrapper">
                                    <span class="retention-progress-label font-bold text-yellow">83%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </body>
</html>