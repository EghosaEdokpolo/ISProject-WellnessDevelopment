<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event_guests')) {
            return;
        }

        Schema::table('event_guests', function (Blueprint $table): void {
            if (! Schema::hasColumn('event_guests', 'verification_method')) {
                $table->string('verification_method', 30)->default('email_otp')->after('waiver_accepted');
            }
            if (! Schema::hasColumn('event_guests', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_method');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive rollback
    }
};