<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand the events.category column to support all 8 wellness pillars.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `events` MODIFY COLUMN `category` ENUM('physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `events` MODIFY COLUMN `category` ENUM('physical', 'mental', 'financial', 'social') NOT NULL");
    }
};