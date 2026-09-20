<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['email', 'google_calendar', 'sms']);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('recipient_filter')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['queued', 'sending', 'sent', 'failed'])->default('queued');
            $table->integer('recipient_count')->default(0);
            $table->foreignId('sent_by')->constrained('staff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};