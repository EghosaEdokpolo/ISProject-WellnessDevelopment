<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('article_slug');
            $table->timestamps();
            
            // A user can only bookmark an article once
            $table->unique(['user_id', 'article_slug']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_bookmarks');
    }
};