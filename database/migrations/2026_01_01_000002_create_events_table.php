<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // auto-incrementing primary key — Laravel convention
            $table->string('name');
            $table->enum('category', ['physical', 'mental', 'financial', 'social']);
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->string('location');
            $table->unsignedInteger('capacity');

            // foreignId() + constrained() creates a proper foreign key to users.id
            // and gives you $event->createdBy in the model relationship below
            $table->foreignId('created_by')->constrained('users');

            $table->timestamps(); // adds created_at + updated_at automatically
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
