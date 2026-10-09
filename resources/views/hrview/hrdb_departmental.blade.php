<!DOCTYPE html>
<html lang="en">

<head>
<!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="The the Hr / people and culture analytical dashboard used to view metrics relation to staff wellness in strathmore University.">

        <title>Department Engagement Growth</title>

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

                    <a href="{{ route('retention.metrics') }}" class="nav-link">
                       <img src="/images/retention-icon1.png" alt="retention icon" class="nav-icon" id="retention-icon"> Retention
                    </a>

                    <a href="{{ route('department.analysis') }}" class="nav-link active">
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

        <main>
            <!--Title-->
            <h1>Departmental Engagement Growth</h1>

            <p class="caption">Analysis of departments across the acedemic year.</p>
            
            <h2 class="split-row">OVERVIEW</h2>
                
                <div class="dashboard-row dept">

                    <!-- CARD 1: Departments Tracked -->
                    <div class="container metric-card">
                        <div class="card-header-line">
                            <span class="card-label">DEPARTMENTS TRACKED</span>
                        </div>
                        <div class="metric-value text-blue">%</div>
                    </div>

                    <!-- CARD 2: Engaged Departments -->
                    <div class="container metric-card">
                        <div class="card-header-line">
                            <span class="card-label">ENGAGED DEPARTMENTS</span>
                        </div>
                        <div class="metric-value text-blue">%</div>
                    </div>

                </div>

                <!-- wrapping dashboard layout 2 -->
                <div class="dashboard-row dept">

                    <!-- CARD 1: most active -->
                    <div class="container dept-card">
                        <div class="card-header">
                            <span class="card-label">MOST ACTIVE — TOP 5</span>
                        </div>
                        
                        <!-- Row List Wrapper: Contains the list elements -->
                        <div class="dept-list-wrapper">
                            <!-- Individual List Row Item -->
                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: least active -->
                    <div class="container dept-card">
                        <div class="card-header">
                            <span class="card-label">LEAST ACTIVE — BOTTOM 5</span>
                        </div>
                        
                        <!-- Row List Wrapper: Contains the list elements -->
                        <div class="dept-list-wrapper">
                            <!-- Individual List Row Item -->
                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>

                            <div class="dept-list-item">
                                <span class="dept-item-name">help desk</span>
                                <span class="dept-item-value">0%</span>
                            </div>
                        </div>
                    </div>
                </div>


            <h2 class="split-row">RANKED TABLE</h2>

                <div class="table-container2">
                        <table>
                            <!--row 1-->
                            <thead>
                                <tr>
                                    <th scope="col">Number(#)</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Staff</th>
                                    <th scope="col">participant number</th>
                                    <th scope="col">Participation Rate</th>
                                    <th scope="col">Engagement status</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                        
                            <!--row 2-->
                                <tr>
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                </tr>
                            <!--row 2-->
                                <tr>
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                    <!-- number column -->
                                    <td class="name-column">1</td>
                                    <!-- department column -->
                                    <td class="department-column">help desk</td>
                                    
                                </tr>
                            
                             
                            
                            <!-- <tr>
                                <td colspan="9">No registrations found.</td>
                            </tr> -->
                            

                            </tbody>
                        </table>
                    </div>

                <button type="submit" id="export">Export Report (csv)</button>
        </main>
    </body>
</html>