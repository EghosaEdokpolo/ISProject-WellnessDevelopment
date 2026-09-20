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
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('points');
            $table->string('reason');
            $table->string('academic_year');
            $table->timestamps();
            $table->index(['staff_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};