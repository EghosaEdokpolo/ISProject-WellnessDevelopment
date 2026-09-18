<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name', 'category', 'description', 'starts_at', 'location', 'capacity', 'created_by',
    ];

    // POINTER: casts() tells Eloquent to automatically convert this database
    // column into a real PHP Carbon date object whenever you read it, and back
    // into a proper datetime string whenever you save it. Without this,
    // $event->starts_at would just be a plain string.
    protected function casts(): array
    {
        return ['starts_at' => 'datetime'];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // POINTER: an "accessor" — lets you write $event->spots_remaining in a
    // Blade view like it's a normal column, but it's actually calculated
    // live from capacity minus how many people have registered.
    public function getSpotsRemainingAttribute(): int
    {
        return max(0, $this->capacity - $this->registrations()->count());
    }

    // Tailwind class per category, used to colour the pastel badges in Blade
    // (kept here so every view stays consistent — one source of truth)
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'physical' => 'bg-wl-lavender text-wl-indigo',
            'mental' => 'bg-wl-mint text-emerald-700',
            'financial' => 'bg-wl-cream text-amber-700',
            'social' => 'bg-wl-lilac text-purple-700',
        };
    }
}
