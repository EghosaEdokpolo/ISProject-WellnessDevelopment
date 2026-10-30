<!DOCTYPE html>
<html lang="en">

<head>
<!--meta data about the page that isnt seen-->
        <meta charset="UTF-8">
        <meta name="author" content="group 1">
        <meta name="description" content="The the Hr / people and culture analytical dashboard used to view metrics relation to staff wellness in strathmore University.">

        <title>Event Management</title>

        <!--favicon-->
        <link rel="icon" href="../assets/Strathmore_uni_logo-notext.png" type="image/x-icon">
        
        <!-- Laravel Vite directive to load compiled application bundles if needed -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/hr_view_style.css', 'resources/js/app.js']); ?>
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

                    <a href="<?php echo e(route('dashboard.hr')); ?>" class="nav-link"> 
                        <img src="/images/analytics-icon.png" alt="analytics icon" class="nav-icon"> Overview
                    </a> 

                    <a href="<?php echo e(route('impact.analytics')); ?>" class="nav-link">
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
                <a href="<?php echo e(route('attendees.index')); ?>" class="nav-link">
                    <img src="/images/attendance-icon.png" alt="attendees icon" class="nav-icon">Attendees
                </a> 

                <a href="<?php echo e(route('events.management')); ?>" class="nav-link active">
                    <img src="/images/events-icon.png" alt="events icon" class="nav-icon"> Events
                </a> 

                <a href="<?php echo e(route('settings.edit')); ?>" class="nav-link">
                    <img src="/images/settings-icon.png" alt="settings icon" class="nav-icon">Settings
                </a>
            </nav>
        </header>

        <!--Title-->
        <h1>Manage Events</h1>

        <main>
            <!-- 1. The structural flex layout wrapper row starts here -->
            <div class="dashboard-row"> 
            <div class="dashboard-rowspecial">
                
                <!-- FIRST CARD: Create and Publish -->
                <div class="container candpevents">
                    <h2 class="cardhead">Create & Publish Events</h2>
                    
                    <form action="<?php echo e(route('events.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?> <!-- Secure Token Validation -->
                        <p>
                            <label for="eventName" class="form_labels">Event Name <br></label>
                            <input type="text" name="eventName" id="eventName" required>
                        </p>

                        <p>
                            <label for="date" class="form_labels">Date<br></label>
                            <input type="datetime-local" name="date" id="date" required>
                        </p>
                        
                        <p>
                            <label for="location" class="form_labels">Location <br></label>
                            <input type="text" name="location" id="location" required placeholder="Room/Venue">
                        </p>

                        <p class="form_labels">Wellness Category</p>
                        <div>
                            <input type="radio" name="wellcat" id="physical" value="physical" required>
                            <label for="physical">Physical</label>

                            <input type="radio" name="wellcat" id="mental" value="mental">
                            <label for="mental">Mental</label>
                            
                            <input type="radio" name="wellcat" id="financial" value="financial">
                            <label for="financial">Financial</label>
                            
                            <input type="radio" name="wellcat" id="social" value="social">
                            <label for="social">Social</label>
                        </div>
                        
                        <p>
                            <label for="description" class="form_labels">Description</label> <br>
                            <textarea name="description" id="description" cols="30" rows="3" required></textarea>
                        </p>

                        <p>
                            <label class="form_labels">Points Awarded (on check-in)</label>
                            <input type="number" id="pa_checkin" name="points_awarded_checkin" value="5" min="0">
                        </p>

                        <p>
                            <label class="form_labels">Points Awarded (on feedback)</label>
                            <input type="number" id="pa_feedback" name="points_awarded_feedback" value="5" min="0">
                        </p>

                        
                        <button type="submit" id="publish">Publish Event</button>
                    </form>
                </div> <!-- Closes Card 1 safely -->

                <!-- SECOND CARD: Send Awareness Communication -->
                <div class="container awareness-box">
                    <h2 class="cardhead">Send Awareness Communication</h2>

                    <p class="minidescription">Email + Google Calendar invite to all selected staff members.</p>

                        <!-- after the event has been made, the db will have the event
                        details that can be selected for sending the specific communication. -->
                <form action="https://httpbin.org/post" method="post">
                <p>
                <label for="eventcategory" class="form_labels">Select An Event To Send An Email For</label> <br>
                        <input type="text" name="eventcategory" id="eventcategory" list="eventlist" required>    
                    <datalist id="eventlist">
                            <option value="Gotten from DB">
                    </datalist>
                </p>

                <p>
                    <!-- invitee selection -->
                    <label for="emailcategory" class="form_labels">Select Invitee Category</label> <br>
                        <input type="text" name="emailcategory" id="emailcategory" list="invitelist" required>    
                    <datalist id="invitelist">
                            <option value="All staff">
                            <option value="Female staff">
                            <option value="Male staff">
                            <option value="Cafeteria staff">
                            <option value="Housekeeping staff">
                            <option value="SCES">
                            <option value="STH">
                    </datalist>
                </p>


                    <!-- display the venue from the selected event. -->
                    
                        <span class="form_labels">Event Details</span> <br>
                            <div class="mini_container">
                                <div class="dynamic-scroll-wrapper">
                                <!-- Event details will be dynamically populated here from the database -->
                                </div>
                            </div>

                        <button type="submit" id="publish"><img src="/images/send_mail.png" alt="send mail icon" class="nav-icon">Send Email + Calendar Invite</button>
                </form>

                </div> <!-- Closes Card 2 safely -->

                <!-- THIRD CARD: QR code generation i guess? -->
                <div class="container awareness-box">
                    <h2>QR Code</h2>
                    <p class="minidescription">QR code to be shared for day of event check-in</p>
                    
                    <div class="mini_container" style="display: flex; justify-content: center; align-items: center; padding: 20px; background: #f9fafb; border-radius: 8px; min-height: 220px;">
                        <div id="qrcode-canvas"></div>
                    </div>

                    <p id="qr-meta-text" style="text-align: center; font-size: 13px; color: #6b7280; margin-top: 10px; font-weight: 500;">
                        Select an event below to display its check-in code.
                    </p>

                    <!-- APPLYING YOUR EXACT requested ID and custom style layout overrides here! -->
                    <button type="button" id="download-qr-btn" class="btn" style="display: none; justify-content: center; align-items: center; width: 100%; margin: auto; padding: 12px; font-size: large; background-color: rgb(204, 156, 74); color: black; font-weight: bold; border: none; border-radius: 7px; cursor: pointer; margin-top: 10px;">
                        Download QR Code Image
                    </button>
                </div><!-- Closes Card 3 safely -->

                <!-- OPTIONAL CARD:  -->
                <div class="container awareness-box">
                    <h2 class="cardhead">Upcoming Events</h2>

                    <p class="caption">Upcoming scheduled events and past events. (past events can be deleted if they are no longer relevant)</p>
                    
                    <div class="events-list">
                        <?php $__empty_1 = true; $__currentLoopData = $upcomingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="event-item-row" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #eee;">
                                <div class="event-details">
                                    <span class="event-title" style="font-weight: bold; color: #02338d;"><?php echo e($event->name); ?></span> <br>
                                    <span class="event-meta" style="font-size: 12px; color: #6b7280;">
                                        📅 <?php echo e(\Carbon\Carbon::parse($event->starts_at)->format('D, d M Y')); ?> | 📍 <?php echo e($event->location); ?> <br>
                                        🎟️ Check-in: <strong><?php echo e($event->points_checkin); ?> pts</strong> | 📝 Feedback: <strong><?php echo e($event->points_feedback); ?> pts</strong>
                                    </span>
                                </div>

                                <!-- ACTION BUTTONS ROW -->

                                
                                 <div style="display: flex; gap: 8px; align-items: center;">
                                    
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <!-- EDIT LINK: Moves them to an edit screen -->
                                    <a href="<?php echo e(route('events.edit', $event->id)); ?>" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 13px;">
                                        Edit
                                    </a>

                                    <!-- QR CODE GENERATION BUTTON -->
                                    <button type="button" onclick="generateEventQR('<?php echo e($event->id); ?>', '<?php echo e(addslashes($event->name)); ?>')" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; display: inline-block !important;">
                                        View QR
                                    </button>

                                    <!-- DELETE WORKFLOW -->
                                    <form action="<?php echo e(route('events.destroy', $event->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this event?');" style="margin: 0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div style="padding: 20px; text-align: center; color: #888;">
                                No upcoming events registered inside the database system.
                            </div>
                        <?php endif; ?>
                    </div>
                </div> <!-- Closes Card 4 safely -->
                
            </div> <!-- This closes dashboard-rowspecial -->
            </div> <!-- This closes dashboard-row -->
</main>
        <!-- 4. LOAD EXTERNAL QR LIBRARY DEPENDENCY AND ISOLATED MODULE SCRIPT VIA VITE AT BOTTOM FOR REMEMBERING -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/hr_events_qr.js']); ?>

       

    </body>
</html><?php /**PATH C:\Users\Eggy\Desktop\YEAR-4\ISProject-WellnessDevelopment\resources\views/hrview/hrdb_eventsmanagement.blade.php ENDPATH**/ ?>