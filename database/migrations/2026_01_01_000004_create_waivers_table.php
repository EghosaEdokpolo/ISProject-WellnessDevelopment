<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('signature_name'); // typed full name, as in your prototype
            $table->timestamp('signed_at');
            $table->string('ip_address')->nullable(); // basic proof-of-signing trail
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waivers');
    }
};
