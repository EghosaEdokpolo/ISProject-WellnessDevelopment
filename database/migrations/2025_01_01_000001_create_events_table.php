<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('wellness_category', ['physical', 'mental', 'financial', 'social']);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('venue')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('points_awarded')->default(10);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('staff');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['wellness_category', 'starts_at']);
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};