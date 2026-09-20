<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->boolean('qr_checked_in')->default(false);
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'staff_id']);
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendance');
    }
};