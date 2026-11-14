<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- csrf token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Settings - WPMIS</title>
    <link rel="icon" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}">
    
     <!-- Laravel Vite directive to load compiled application bundles if needed -->
        @vite(['resources/css/hr_view_style.css', 'resources/css/hr_settings.css', 
        'resources/js/app.js', 'resources/js/hr_settings.js'])
    
</head>
<body>
    <div class="header-title">
        <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" alt="Strathmore Logo">
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

                <a href="{{ route('settings.edit') }}" class="nav-link active">
                    <img src="/images/settings-icon.png" alt="settings icon" class="nav-icon">Settings
                </a>
            </nav>
    </header>

    <h1>Settings</h1>

    <div id="errorBanner" class="error-banner"></div>
    <div id="successBanner" class="success-banner"></div>

    <div class="grid">
        <div class="card">
            <h2>Points Reset Control</h2>
            <p class="desc">Points should be reset annually. This action is irreversible; all staff balances will be zeroed.</p>

            <div class="info-box">
            <div class="row">
                <span class="key">Current Cycle</span>
                <!-- Accessing the array data passed from the controller -->
                <span class="val" id="currentYear">{{ $points['current_academic_year'] }}</span>
            </div>
            <div class="row">
                <span class="key">Next Reset</span>
                <span class="val" id="nextReset">{{ $points['next_reset'] }}</span>
            </div>
            <div class="row">
                <span class="key">Total Points In Current Cycle</span>
                <span class="val" id="currentPoints">{{ number_format($points['total_active_points']) }}</span>
            </div>
        </div>

            <button class="btn btn-reset" id="resetBtn">Reset All Staff Points</button>
        </div>

        <div class="card">
            <h2>Data Protection Details</h2>
            <p class="desc">Kenya Data Protection Act, 2019 compliance information.</p>

            <table class="dp-table">
            <tbody>
                <tr>
                    <td>Data Controller</td>
                    <td id="dpController">{{ $dataProtection['controller'] }}</td>
                </tr>

                <tr>
                    <td>Applicable Law</td>
                    <td id="dpLaw">{{ $dataProtection['applicable_law'] }}</td>
                </tr>
                <tr>
                    <td>Retention Period</td>
                    <td id="dpRetention">{{ $dataProtection['retention_period'] }}</td>
                </tr>
                <tr>
                    <td>Contact</td>
                    <td id="dpContact">{{ $dataProtection['contact'] }}</td>
                </tr>
                <tr>
                    <td>DPIA Status</td>
                    <td id="dpDpia">{{ $dataProtection['dpia_status'] }}</td>
                </tr>
            </tbody>
        </table>

            <button class="btn btn-logout" id="logoutBtn" style="margin-top:24px;">Logout</button>
        </div>
    </div>

    <!-- Scoped Component Module JavaScript -->
    <script type="module" src="{{ asset('js/hr_settings.js') }}"></script>
</body>
</html>
