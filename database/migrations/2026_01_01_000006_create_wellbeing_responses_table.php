<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellbeing_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', ['pre', 'post', 'follow_up']);

            // The 5 WHO-5 statements, each scored 0-4 by the person answering.
            // Storing each item (not just the total) lets you re-analyze later
            // without needing to redo the survey.
            $table->unsignedTinyInteger('item_cheerful');
            $table->unsignedTinyInteger('item_calm');
            $table->unsignedTinyInteger('item_active');
            $table->unsignedTinyInteger('item_rested');
            $table->unsignedTinyInteger('item_interested');

            // total_score is 0-25 (sum of the 5 items above, each 0-4).
            // We store it directly instead of recalculating every time it's displayed —
            // a small denormalization that keeps dashboard queries fast.
            $table->unsignedTinyInteger('total_score');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellbeing_responses');
    }
};
