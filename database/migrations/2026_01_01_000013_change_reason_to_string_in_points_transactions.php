<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN reason VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN reason ENUM('check_in', 'feedback', 'consent', 'referral') NOT NULL");
    }
};