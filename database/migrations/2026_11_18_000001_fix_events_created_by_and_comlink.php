<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (! Schema::hasColumn('events', 'comlink')) {
                $table->string('comlink', 500)->nullable()->after('location');
            }

            if (! Schema::hasColumn('events', 'whatsapp_channel_url')) {
                $table->string('whatsapp_channel_url', 500)->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        // Safe no-op to preserve production event columns
    }
};