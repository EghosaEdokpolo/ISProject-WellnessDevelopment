<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('registrations', 'consent_given')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->boolean('consent_given')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('registrations', 'consent_given')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropColumn('consent_given');
            });
        }
    }
};