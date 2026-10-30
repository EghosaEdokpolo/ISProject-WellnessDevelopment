<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Attendees - WPMIS</title>
    <link rel="icon" href="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>">
    
    <!-- Base Layout CSS Styles -->
    <!-- <link rel="stylesheet" href="<?php echo e(asset('css/hr_view_style.css')); ?>"> -->
     
    <!-- Scoped Component Stylesheet -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/hr_view_style.css','resources/js/app.js',
        'resources/css/hr_attendees.css','resources/js/hr_attendees.js'
    ]); ?>
</head>
<body>
    <div class="header-title">
        <img src="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>" alt="Strathmore Logo">
        Strathmore University - Wellness Programme Management & Impact System (WPMIS)
    </div>
    
    <header class="nav-head">
            <!--first category at the top of the page-->
            <!--navigation links-->
            <nav class="nav-menu">
                <div class="pillgroup1">
                    <span class="category-label">Dashboards</span> 

                    <a href="<?php echo e(route('dashboard.hr')); ?>" class="nav-link"> 
                        <img src="/images/analytics-icon.png" alt="analytics icon" class="nav-icon"> Overview
                    </a> 

                    <a href="<?php echo e(route('impact.analytics')); ?>" class="nav-link">
                        <img src="/images/Impact-icon.png" alt="impact icon" class="nav-icon" id="impact-icon">Impact
                    </a> 

                    <a href="<?php echo e(route('retention.metrics')); ?>" class="nav-link">
                       <img src="/images/retention-icon1.png" alt="retention icon" class="nav-icon" id="retention-icon"> Retention
                    </a>

                    <a href="<?php echo e(route('department.analysis')); ?>" class="nav-link">
                        <img src="/images/department-icon.png" alt="department icon" class="nav-icon">Departmental
                    </a>
                </div>
                
                <!--second category at the top of the page-->
                <a href="<?php echo e(route('attendees.index')); ?>" class="nav-link active">
                    <img src="/images/attendance-icon.png" alt="attendees icon" class="nav-icon">Attendees
                </a> 

                <a href="<?php echo e(route('events.management')); ?>" class="nav-link">
                    <img src="/images/events-icon.png" alt="events icon" class="nav-icon"> Events
                </a> 

                <a href="<?php echo e(route('settings.edit')); ?>" class="nav-link">
                    <img src="/images/settings-icon.png" alt="settings icon" class="nav-icon">Settings
                </a>
            </nav>
    </header>

    <h1>Attendee Table</h1>
    <p class="caption" style="padding-left:4%; color:#6b7280;">Full record of staff participation per event.</p>

    <div id="errorBanner" class="error-banner"></div>

    <div class="selector">
        <label style="font-weight:600;">Select Event:</label>
        <select id="eventSelect">
            <!-- DYNAMIC BLADE ROLL: Loads instantly on initial load server-side! -->
            <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <option value="<?php echo e($event->id); ?>">
                    <?php echo e($event->name); ?> — <?php echo e(\Carbon\Carbon::parse($event->starts_at)->format('d/m/Y')); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <option value="">No events found inside database system</option>
            <?php endif; ?>
        </select>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="attendeeTable" style="display:none;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Registered</th>
                    <th>Consent</th>
                    <th>Feedback</th>
                    <th>WHO-5 Pre</th>
                    <th>WHO-5 Post</th>
                    <th>Points</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="attendeeRows"></tbody>
        </table>
        <div class="empty" id="emptyMsg">Select an event to view attendees.</div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\Eggy\Desktop\YEAR-4\ISProject-WellnessDevelopment\resources\views/hrview/hrdb_attendeetable.blade.php ENDPATH**/ ?>