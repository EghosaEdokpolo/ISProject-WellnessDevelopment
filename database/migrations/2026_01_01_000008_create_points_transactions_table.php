<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('reason', ['check_in', 'feedback', 'consent', 'referral']);
            $table->integer('points'); // signed, so future deductions/redemptions are possible
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
