<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('article_slug');
            $table->timestamps();
            
            // Prevent duplicate saves
            $table->unique(['user_id', 'article_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_articles');
    }
};