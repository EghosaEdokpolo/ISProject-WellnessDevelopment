<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Check if the columns exist before trying to add them
            if (!Schema::hasColumn('registrations', 'dietary_requirements')) {
                $table->string('dietary_requirements')->nullable()->after('status');
            }
            if (!Schema::hasColumn('registrations', 'consent_given')) {
                $table->boolean('consent_given')->default(false)->after('dietary_requirements');
            }
            if (!Schema::hasColumn('registrations', 'consent_given_at')) {
                $table->timestamp('consent_given_at')->nullable()->after('consent_given');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['dietary_requirements', 'consent_given', 'consent_given_at']);
        });
    }
};