<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand check_ins.method to support 'qr', 'qr_scan', 'manual', and 'hr_retroactive',
     * and ensure events.category supports all 8 Strathmore wellness pillars.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `check_ins` MODIFY COLUMN `method` VARCHAR(50) NOT NULL DEFAULT 'manual'");
        DB::statement("ALTER TABLE `events` MODIFY COLUMN `category` ENUM('physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `check_ins` MODIFY COLUMN `method` ENUM('qr', 'manual') NOT NULL DEFAULT 'manual'");
        DB::statement("ALTER TABLE `events` MODIFY COLUMN `category` ENUM('physical', 'mental', 'financial', 'social') NOT NULL");
    }
};