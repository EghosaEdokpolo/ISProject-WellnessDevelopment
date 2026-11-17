<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contents')) {
            Schema::create('contents', function (Blueprint $table): void {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title');
                $table->string('category')->default('physical');
                $table->text('excerpt')->nullable();
                $table->longText('body');
                $table->string('image_url')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('contents', function (Blueprint $table): void {
                if (!Schema::hasColumn('contents', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('contents', 'title')) {
                    $table->string('title')->after('slug');
                }
                if (!Schema::hasColumn('contents', 'category')) {
                    $table->string('category')->default('physical')->after('title');
                }
                if (!Schema::hasColumn('contents', 'excerpt')) {
                    $table->text('excerpt')->nullable()->after('category');
                }
                if (!Schema::hasColumn('contents', 'body')) {
                    $table->longText('body')->after('excerpt');
                }
                if (!Schema::hasColumn('contents', 'image_url')) {
                    $table->string('image_url')->nullable()->after('body');
                }
                if (!Schema::hasColumn('contents', 'is_published')) {
                    $table->boolean('is_published')->default(false)->after('image_url');
                }
                if (!Schema::hasColumn('contents', 'created_at') && !Schema::hasColumn('contents', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }

        if (!Schema::hasTable('content_user')) {
            Schema::create('content_user', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['content_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_user');
        Schema::dropIfExists('contents');
    }
};