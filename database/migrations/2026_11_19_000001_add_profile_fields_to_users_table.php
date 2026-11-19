<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'residence')) {
                $table->string('residence', 180)->nullable()->after('department');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('residence');
            }
            if (! Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path', 255)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive rollback for production safety
    }
};