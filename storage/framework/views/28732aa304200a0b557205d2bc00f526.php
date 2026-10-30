<!DOCTYPE html>
<html lang="en">

<head>
<!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="group 1">
        <meta name="description" content="The the Hr / people and culture analytical dashboard used to view metrics related to staff wellness in strathmore University.">

        <title>Dashboard-HR</title>

        <!--favicon-->
        <link rel="icon" type="image/png" href="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>?v=2">
        
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/hr_view_style.css', 'resources/js/app.js']); ?>

    </head>

    <body>
        <!--data about the page that is seen-->
            <div class="header-title">
                <img src="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>" alt="Strathmore University Logo">
                Strathmore University - Wellness Programme Management & Impact System (WPMIS)
            </div>
        <header class="nav-head">

            <!--first category at the top of the page-->
            <!--navigation links-->
            <nav class="nav-menu">
                <div class="pillgroup1">
                    <span class="category-label">Dashboards</span> 

                    <a href="<?php echo e(route('dashboard.hr')); ?>" class="nav-link active"> 
                        <img src="<?php echo e(asset('images/analytics-icon.png')); ?>" alt="analytics icon" class="nav-icon"> Overview
                    </a> 

                    <a href="<?php echo e(route('impact.analytics')); ?>" class="nav-link">
                        <img src="<?php echo e(asset('images/Impact-icon.png')); ?>" alt="impact icon" class="nav-icon" id="impact-icon">Impact
                    </a> 

                    <a href="<?php echo e(route('retention.metrics')); ?>" class="nav-link">
                       <img src="<?php echo e(asset('images/retention-icon1.png')); ?>" alt="retention icon" class="nav-icon" id="retention-icon"> Retention
                    </a>

                    <a href="<?php echo e(route('department.analysis')); ?>" class="nav-link">
                        <img src="<?php echo e(asset('images/department-icon.png')); ?>" alt="department icon" class="nav-icon">Departmental
                    </a>
                </div>
                
                <!--second category at the top of the page-->
                <a href="<?php echo e(route('attendees.index')); ?>" class="nav-link">
                    <img src="<?php echo e(asset('images/attendance-icon.png')); ?>" alt="attendees icon" class="nav-icon">Attendees
                </a> 

                <a href="<?php echo e(route('events.management')); ?>" class="nav-link">
                    <img src="<?php echo e(asset('images/events-icon.png')); ?>" alt="events icon" class="nav-icon"> Events
                </a> 

                <a href="<?php echo e(route('settings.edit')); ?>" class="nav-link">
                    <img src="<?php echo e(asset('images/settings-icon.png')); ?>   " alt="settings icon" class="nav-icon">Settings
                </a>
            </nav>
        </header>

        <main>
    <!--Title-->
    <h1>HR Overview Dashboard</h1>
    
    <!--page details-->
    
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

    <!-- LEADING INDICATORS LAYER -->
    <h2 class="section-row-title">LEADING INDICATORS - SYSTEM ADOPTION</h2>
    
    <div class="dashboard-row">

        <!-- CARD 1: Registration Rate -->
        <div class="container metric-card">
            <div class="card-header-line">
                <span class="card-label">REGISTRATION RATE</span>
                <span class="badge badge-leading">
                    <span class="badge-dot"></span> Leading
                </span>
            </div>
            <div class="metric-value text-green"><?php echo e($registrationRate); ?>%</div>
            <div class="card-subtext"> <?php echo e($totalStaff); ?> staff member(s)</div>
        </div>

        <!-- CARD 2: Points Earned -->
        <div class="container metric-card">
            <div class="card-header-line">
                <span class="card-label">POINTS EARNED</span>
                <span class="badge badge-leading">
                    <span class="badge-dot"></span> Leading
                </span>
            </div>
            <div class="metric-value"><?php echo e(number_format($totalPointsEarned)); ?></div>
            <div class="card-subtext">this academic year across all staff</div>
        </div>

        <!-- CARD 3: Events This Semester -->
        <div class="container metric-card">
            <div class="card-header-line">
                <span class="card-label">EVENTS SCHEDULED</span>
                <span class="badge badge-leading">
                    <span class="badge-dot"></span> Leading
                </span>
            </div>
            
            <!-- This variable now displays the smart time-filtered academic year total -->
            <div class="metric-value"><?php echo e($eventsThisSemester); ?></div>
            
            <!-- UPDATE THIS SUBTEXT LINK: Clear documentation of the tracking loop -->
            <div class="card-subtext">Active programs this academic year</div>
        </div>

    </div>

    <!--  Lagging Indicators row starts here  -->
    <h2 class="section-row-title">LAGGING INDICATORS — WELLNESS OUTCOMES</h2>

    <!-- wrapping dashboard layout row -->
    <div class="dashboard-row">

        <!-- CARD 1: Wellness Impact Score -->
        <div class="container metric-cardlag">
            <div class="card-header-line">
                <span class="card-label">WELLNESS IMPACT SCORE</span>
                <span class="badge badge-lagging">
                    <span class="badge-dot"></span> Lagging
                </span>
            </div>
            <!-- Large trend metric indicator -->
            <div class="metric-value"><?php echo e($avgLift > 0 ? '+' : ''); ?><?php echo e($avgLift); ?></div>
            <div class="card-subtext">
                avg WHO-5 pre→post delta · <a href="<?php echo e(route('impact.analytics')); ?>" class="dashboard-inline-link">View Impact Dashboard →</a>
            </div>
        </div>

        <!-- CARD 2: Sustained Participation -->
        <div class="container metric-cardlag">
    <div class="card-header-line">
        <span class="card-label">SUSTAINED PARTICIPATION</span>
        <span class="badge badge-lagging">
            <span class="badge-dot"></span> Lagging
        </span>
    </div>
        
        <!-- Prints out the dynamically calculated engagement percentage -->
        <div class="metric-value text-purple"><?php echo e($sustainedParticipationRate); ?>%</div>
        
        <div class="card-subtext">
            attended 2+ events · <a href="<?php echo e(route('retention.metrics')); ?>" class="dashboard-inline-link">View Retention Dashboard →</a>
        </div>
    </div>


            <!-- SECTION ROW 3: ANALYTICS & RECENT ACTIVITY -->
        <div class="dashboard-row split-row">

            <!-- COLUMN 1: Wellness Category Analytics Chart Box -->
            <div class="container analytics-chart-card">
                <h2>Wellness Category Distribution</h2>
                <p class="chart-sub">Total sign ups allocated by category this academic year.</p>
                
                <!-- THE FLEXBOX BAR CHART CANVAS -->
                
                <div class="bar-chart-canvas">
                    
                    <!-- Bar 1: Physical -->
                    <div class="chart-column-track">
                        <!-- 1. IF LESS THAN 10: Render value outside (above) the bar -->
                        <?php if($physicalCount > 0 && $physicalCount < 10): ?>
                            <span class="bar-value-outside" style="margin-bottom: 4px; font-weight: bold; font-size: 11px; display: block; text-align: center;"><?php echo e($physicalCount); ?></span>
                        <?php endif; ?>

                        <div class="chart-bar bar-physical" style="height: <?php echo e(min(($physicalCount / 50) * 120, 120)); ?>px; display: flex; align-items: center; justify-content: center;">
                            <!-- 2. IF 10 OR MORE: Render value cleanly inside the bar -->
                            <?php if($physicalCount >= 10): ?>
                                <span class="bar-value"><?php echo e($physicalCount); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="bar-label">Physical</span>
                    </div>

                    <!-- Bar 2: Mental -->
                    <div class="chart-column-track">
                        <?php if($mentalCount > 0 && $mentalCount < 10): ?>
                            <span class="bar-value-outside" style="margin-bottom: 4px; font-weight: bold; font-size: 11px; display: block; text-align: center;"><?php echo e($mentalCount); ?></span>
                        <?php endif; ?>

                        <div class="chart-bar bar-mental" style="height: <?php echo e(min(($mentalCount / 50) * 120, 120)); ?>px; display: flex; align-items: center; justify-content: center;">
                            <?php if($mentalCount >= 10): ?>
                                <span class="bar-value"><?php echo e($mentalCount); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="bar-label">Mental</span>
                    </div>

                    <!-- Bar 3: Financial -->
                    <div class="chart-column-track">
                        <?php if($financialCount > 0 && $financialCount < 10): ?>
                            <span class="bar-value-outside" style="margin-bottom: 4px; font-weight: bold; font-size: 11px; display: block; text-align: center;"><?php echo e($financialCount); ?></span>
                        <?php endif; ?>

                        <div class="chart-bar bar-financial" style="height: <?php echo e(min(($financialCount / 50) * 120, 120)); ?>px; display: flex; align-items: center; justify-content: center;">
                            <?php if($financialCount >= 10): ?>
                                <span class="bar-value"><?php echo e($financialCount); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="bar-label">Financial</span>
                    </div>

                    <!-- Bar 4: Social -->
                    <div class="chart-column-track">
                        <?php if($socialCount > 0 && $socialCount < 10): ?>
                            <span class="bar-value-outside" style="margin-bottom: 4px; font-weight: bold; font-size: 11px; display: block; text-align: center;"><?php echo e($socialCount); ?></span>
                        <?php endif; ?>

                        <div class="chart-bar bar-social" style="height: <?php echo e(min(($socialCount / 50) * 120, 120)); ?>px; display: flex; align-items: center; justify-content: center;">
                            <?php if($socialCount >= 10): ?>
                                <span class="bar-value"><?php echo e($socialCount); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="bar-label">Social</span>
                    </div>

                </div>





            </div>

            <!-- COLUMN 2: Recent Activity Timeline Log Feed -->
            <div class="container activity-log-card2">
            <div class="dashboard-section-title2">
                <h2>Recent Activity Log</h2>
                <p class="chart-sub">Real-time system adoption updates</p>
            </div>
            
            <div class="timeline-container">
                <!-- Starts the loop cleanly -->
                <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="timeline-item">
                        <div class="timeline-marker <?php echo e($activity['marker']); ?>"></div>
                        <div class="timeline-content">
                            <p class="log-text"><?php echo $activity['text']; ?></p>
                            <span class="log-time"><?php echo e($activity['time']); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <!-- Fallback if your database tables are completely fresh and empty -->
                    <p class="log-text" style="color: #000000; text-align: center; padding-top: 20px;">
                       <strong>No recent system updates recorded yet.</strong>
                    </p>
                <!-- Closes the loop cleanly -->
                <?php endif; ?>
            </div>
        </div> <!-- This closes container activity-log-card2 -->

        </div> <!-- This closes dashboard-row split-row -->
    </main>

    </body>
</html><?php /**PATH C:\Users\Eggy\Desktop\YEAR-4\ISProject-WellnessDevelopment\resources\views/hrview/hrdb_overview.blade.php ENDPATH**/ ?>