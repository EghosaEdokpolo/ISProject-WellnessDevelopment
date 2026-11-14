<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name', 'category', 'description', 'starts_at', 'location', 'capacity', 'created_by',
    ];

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

    public function getSpotsRemainingAttribute(): int
    {
        // OPTIMIZATION: Use the pre-loaded count if available, otherwise query
        $count = $this->registrations_count ?? $this->registrations()->count();
        return max(0, $this->capacity - $count);
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'physical' => 'bg-wl-lavender text-wl-indigo',
            'mental' => 'bg-wl-mint text-emerald-700',
            'financial' => 'bg-wl-cream text-amber-700',
            'social' => 'bg-wl-lilac text-purple-700',
            default => 'bg-gray-100 text-gray-700'
        };
    }
}