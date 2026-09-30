<!DOCTYPE html>
<html lang="en">

<head>
<!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="The the Hr / people and culture analytical dashboard used to view metrics relation to staff wellness in strathmore University.">

        <title>Attendees</title>

        <!--favicon-->
        <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
        
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        @vite(['resources/css/hr_view_style.css', 'resources/js/app.js'])
    </head>

    <body>
        <!--data about the page that is seen-->
        <div class="header-title">
            <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" alt="Strathmore University Logo">
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

                    <a href="#" class="nav-link">
                       <img src="/images/retention-icon1.png" alt="retention icon" class="nav-icon" id="retention-icon"> Retention
                    </a>

                    <a href="#" class="nav-link">
                        <img src="/images/department-icon.png" alt="department icon" class="nav-icon">Departmental
                    </a>
                </div>
                
                <!--second category at the top of the page-->
                <a href="{{ route('attendees.index') }}" class="nav-link active">
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
            <h1>Attendee Table</h1>

            <!--caption is subject to change-->
                <p class="caption">Full record of staff participation</p>

            <!--page details-->

            


            <!--Table-->
            <div class="dashboard-row">

                <!-- table selection area -->
            <div class="container tableSelection">
                <p>
                    <!-- event selection -->
                    <label for="eventSelection" class="form_labels">Select Listed Event</label> <br>
                        <input type="text" name="eventSelection" id="eventSelection" list="eventlist" required>    
                            <datalist id="eventlist">
                                    <option value="Financial Literacy">
                                    <option value="Physical Wellbeing">
                                    <option value="Mental selfcare">
                            </datalist>
                </p>

                <p>
                    <!-- department selection -->
                    <label for="emailcategory" class="form_labels">Filter By</label> <br>
                        <input type="text" name="emailcategory" id="emailcategory" list="invitelist" required>    
                            <datalist id="invitelist">
                                    <option value="All staff">
                                    <option value="Female staff">
                                    <option value="Male staff">
                                    <option value="SCES">
                                    <option value="STH">
                            </datalist>
                </p>
            </div>

                    <div class="table-container">
                        <table>
                            <!--row 1-->
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Attended</th>
                                    <th scope="col">Consent</th>
                                    <th scope="col">Feedback provided</th>
                                    <th scope="col">WHO-5 Pre Assesment</th>
                                    <th scope="col">WHO-5 Post Assesment</th>
                                    <th scope="col">User Points</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($registrations as $reg)
                            <!--row 2-->
                                <tr>
                                    <td class="name-column">{{ $reg->user->name }}</td>
                                    <td class="department-column">{{ $reg->user->department }}</td>
                                    <!-- attended -->
                                    <td class="iconcolumn">
                                        <img src="../images/green-check-icon.png" alt="yes">
                                    </td>
                                    <!-- consent -->
                                    <td class="iconcolumn">
                                        <img src="../images/green-check-icon.png" alt="yes">
                                    </td>
                                    <!-- feedback provided -->
                                    <td class="iconcolumn">
                                        <img src="../images/green-check-icon.png" alt="yes">
                                    </td>
                                    <!-- WHO-5 Pre Assesment -->
                                    <td class="column-text">14</td>
                                    <!-- WHO-5 Post Assesment -->
                                    <td class="column-text">20</td>
                                    <!-- User Points -->
                                    <td class="column-text">45</td>
                                    <!-- Status -->
                                    <td class="column-status1"><span class="col-pill">completed</span></td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="9">No registrations found.</td>
                            </tr>
                            @endforelse

                                <!--row 3-->
                                <tr>
                                    <td class="name-column">Brian Mutiso</td>
                                    <td class="department-column">Finance and Accounting</td>
                                    <!-- attended -->
                                    <td class="iconcolumn">
                                        <img src="../images/green-check-icon.png" alt="yes">
                                    </td>
                                    <!-- consent -->
                                    <td class="iconcolumn">
                                        <img src="../images/green-check-icon.png" alt="yes">
                                    </td>
                                    <!-- feedback provided -->
                                    <td >
                                        -
                                    </td>
                                    <!-- WHO-5 Pre Assesment -->
                                    <td class="column-text">-</td>
                                    <!-- WHO-5 Post Assesment -->
                                    <td class="column-text">-</td>
                                    <!-- User Points -->
                                    <td class="column-text">45</td>
                                    <!-- Status -->
                                    <td class="column-status2"><span class="col-pill">Pending</span></td>
                                </tr>

                                <!--row 4-->
                                <tr>
                                    <td class="name-column">Naomi Chebet</td>
                                    <td class="department-column">School of Law</td>
                                    <!-- attended -->
                                    <td>
                                        -
                                    </td>
                                    <!-- consent -->
                                    <td>
                                        -
                                    </td>
                                    <!-- feedback provided -->
                                    <td>
                                        -
                                    </td>
                                    <!-- WHO-5 Pre Assesment -->
                                    <td class="column-text">-</td>
                                    <!-- WHO-5 Post Assesment -->
                                    <td class="column-text">-</td>
                                    <!-- User Points -->
                                    <td class="column-text">0</td>
                                    <!-- Status -->
                                    <td class="column-status3"><span class="col-pill">Registered</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

            </div>
        </main>
    </body>
</html>