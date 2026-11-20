<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_guests')) {
            return;
        }

        Schema::create('event_guests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('full_name', 150);
            $table->string('email', 150);
            $table->string('phone', 30);
            $table->string('guest_type', 50)->default('external_visitor');
            $table->string('organization', 150)->nullable();
            $table->string('invited_by', 150)->nullable();
            $table->boolean('waiver_accepted')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['event_id', 'email']);
            $table->index(['event_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_guests');
    }
};