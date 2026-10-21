<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            $table->foreignId('registration_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->dropColumn('registration_id');
        });
    }
};