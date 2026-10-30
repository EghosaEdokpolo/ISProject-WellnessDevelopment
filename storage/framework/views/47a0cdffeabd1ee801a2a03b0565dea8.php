<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Attendance Check-In - WPMIS</title>
    <link rel="icon" href="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/hr_events_edit.css']); ?> <!-- Reuse your beautiful edit styling rules! -->
</head>
<body>

    <div class="edit-card-wrapper" style="max-width: 500px; margin: 40px auto; text-align: center;">
        <img src="<?php echo e(asset('assets/Strathmore_uni_logo-notext.png')); ?>" style="width: 70px; margin-bottom: 10px;">
        <h2>Strathmore Wellness Program</h2>
        <p style="color: #6b7280; font-size: 14px;">Event Check-In Terminal Portal</p>

        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left;">
            <p style="margin: 0; font-size: 13px; color: #166534; font-weight: 600;">Active Scanned Workshop Info:</p>
            <!-- PREFILLED PROPERTY FIELDS INJECTED VIA LIVE BLADE TOKENS -->
            <h3 style="margin: 5px 0 0 0; color: #02338d;"><?php echo e($event->name); ?></h3>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #4b5563;">📍 Venue: <?php echo e($event->location); ?></p>
        </div>

        <form action="/hr/checkin/submit/<?php echo e($event->id); ?>" method="POST" style="text-align: left;">
            <?php echo csrf_field(); ?>

            <div class="form-group-block">
                <!-- FIX: Updated label text from email to Staff ID -->
                <label for="staff_id">Enter Strathmore Staff ID Number</label>
                
                <!-- FIX: Switched type to text/number to collect the ID badge directly -->
                <input type="text" id="staff_id" name="staff_id" required placeholder="e.g. 14562 or SU-XXXX" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #ccc; font-weight: 600;">
            </div>

            <button type="submit" class="btn-update-submit" style="background-color: #02338d; color: white; display: flex; justify-content: center; align-items: center; width: 100%; padding: 14px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; margin-top: 15px;">
                Confirm Attendance & Claim Points 🎟️
            </button>
        </form>
    </div>

</body>
</html>
<?php /**PATH C:\Users\Eggy\Desktop\YEAR-4\ISProject-WellnessDevelopment\resources\views/checkin/confirm_form.blade.php ENDPATH**/ ?>