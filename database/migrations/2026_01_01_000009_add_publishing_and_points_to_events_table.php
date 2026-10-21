<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('capacity');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->unsignedInteger('points_awarded')->default(10)->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at', 'points_awarded']);
        });
    }
};