<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// POINTER: Laravel migrations are version-controlled instructions for your database.
// Each file describes ONE change. Laravel runs them in filename (date) order,
// which is why every migration is timestamped. `php artisan migrate` applies them.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // employee_id is what staff type manually at check-in if the QR fails (accessibility fallback)
            $table->string('employee_id')->unique()->nullable()->after('id');

            // enum() restricts the column to only these two values at the DB level
            $table->enum('role', ['staff', 'hr_coordinator'])->default('staff')->after('employee_id');

            $table->string('department')->nullable()->after('role');

            // Running total of wellness points — kept on the user row for fast lookups
            // (the full history/audit trail lives in points_transactions, built later)
            $table->unsignedInteger('points_balance')->default(0)->after('department');
        });
    }

    // down() is the "undo" for this migration — Laravel calls it if you run `migrate:rollback`
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'role', 'department', 'points_balance']);
        });
    }
};
