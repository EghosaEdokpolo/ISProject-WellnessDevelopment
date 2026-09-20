<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->enum('assessment_type', ['pre', 'post']);
            $table->unsignedTinyInteger('who5_score');
            $table->timestamp('assessed_at');
            $table->timestamps();
            $table->unique(['event_id', 'staff_id', 'assessment_type']);
            $table->index(['event_id', 'assessment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_assessments');
    }
};